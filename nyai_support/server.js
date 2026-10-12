const express = require('express');
const cors = require('cors');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

// Path data
const DATA_DIR = path.join(__dirname, 'data');
const AGENTS_FILE = path.join(DATA_DIR, 'agents.json');
const CONNECTORS_FILE = path.join(DATA_DIR, 'connectors.json');
const KNOWLEDGE_FILE = path.join(DATA_DIR, 'knowledge_fallback.json');

// Helper to read / write json
function loadJSON(filePath, defaultVal = []) {
  try {
    if (fs.existsSync(filePath)) {
      return JSON.parse(fs.readFileSync(filePath, 'utf8'));
    }
  } catch (err) {
    console.error(`Error loading ${filePath}:`, err);
  }
  return defaultVal;
}

function saveJSON(filePath, data) {
  try {
    fs.writeFileSync(filePath, JSON.stringify(data, null, 2), 'utf8');
    return true;
  } catch (err) {
    console.error(`Error saving ${filePath}:`, err);
    return false;
  }
}

// In-Memory Storage for Active Conversation Sessions & Activity Logs
const sessions = new Map(); // sessionId -> { id, agentSlug, agentName, currentStepIndex, variables: {}, history: [], department, status, createdAt, updatedAt }
const activityLogs = [];

function logActivity(level, category, message, details = {}) {
  const logItem = {
    id: 'log-' + Date.now() + '-' + Math.floor(Math.random() * 1000),
    timestamp: new Date().toISOString(),
    level, // 'INFO', 'WARN', 'SUCCESS', 'ERROR'
    category, // 'ROUTING', 'WORKFLOW', 'INPUT', 'AGENT'
    message,
    details
  };
  activityLogs.unshift(logItem);
  if (activityLogs.length > 200) activityLogs.pop();
}

// Initial log
logActivity('INFO', 'SYSTEM', 'S-Bot Agentic AI Server initialized successfully for Pemkab Sleman');

// RAGFlow Config Store
let ragflowConfig = {
  baseUrl: 'http://localhost:9380',
  apiKey: 'ragflow-sleman-local-key',
  datasetId: 'sleman-rag-dataset-01',
  systemPrompt: 'Anda adalah Asisten Virtual Cerdas Pemkab Sleman berkarakter ramah, profesional, dan akurat.',
  similarityThreshold: 0.7,
  isLive: false
};

// =========================================================================
// ROUTING LOGIC: Semantic + Fallback Keyword Router
// =========================================================================
function routeIntent(message, agents, preferredSlug = null) {
  const query = (message || '').toLowerCase().trim();

  // If client strictly requests a slug
  if (preferredSlug) {
    const forced = agents.find(a => a.slug === preferredSlug && a.enabled);
    if (forced) {
      return {
        agent: forced,
        confidence: 0.99,
        reason: 'Direct Slug Targeting (' + preferredSlug + ')'
      };
    }
  }

  let bestMatch = null;
  let highestScore = 0;
  let matchReason = '';

  for (const agent of agents) {
    if (!agent.enabled) continue;
    let score = 0;

    // 1. Keyword check
    if (Array.isArray(agent.fallback_keywords)) {
      for (const kw of agent.fallback_keywords) {
        if (query.includes(kw.toLowerCase())) {
          score += 0.35;
        }
      }
    }

    // 2. Trigger check
    const triggerWords = (agent.trigger || '').toLowerCase().split(/\s+/);
    let matchedTriggerWords = 0;
    for (const tw of triggerWords) {
      if (tw.length > 3 && query.includes(tw)) {
        matchedTriggerWords++;
      }
    }
    score += Math.min(0.45, matchedTriggerWords * 0.1);

    // 3. Name & department boost
    if (query.includes(agent.name.toLowerCase())) score += 0.4;
    if (agent.department && query.includes(agent.department.toLowerCase())) score += 0.3;

    if (score > highestScore) {
      highestScore = score;
      bestMatch = agent;
      matchReason = `Matched ${agent.name} (score: ${Math.min(0.98, score.toFixed(2))})`;
    }
  }

  if (highestScore >= 0.35 && bestMatch) {
    return {
      agent: bestMatch,
      confidence: Math.min(0.98, parseFloat(highestScore.toFixed(2))),
      reason: matchReason
    };
  }

  // Fallback to Super Agent 🧠
  return {
    agent: null, // Indicates Super Agent
    confidence: 0.95,
    reason: 'Routed to Super Agent 🧠 (Knowledge Base & General Inquiries)'
  };
}

// Replace template placeholders like {nama_pemohon} with variables
function replaceVariables(text, variables = {}) {
  if (!text) return '';
  return text.replace(/\{(\w+)\}/g, (match, p1) => {
    return variables[p1] !== undefined ? variables[p1] : match;
  });
}

// Validate input for collect_input step
function validateInput(type, value, options = []) {
  const str = String(value || '').trim();
  if (!str) return { valid: false, error: 'Input tidak boleh kosong.' };

  switch (type) {
    case 'email': {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(str)) {
        return { valid: false, error: 'Format email tidak valid. Contoh: nama@gmail.com' };
      }
      return { valid: true, value: str };
    }
    case 'phone': {
      const cleanPhone = str.replace(/[^0-9+]/g, '');
      if (cleanPhone.length < 8) {
        return { valid: false, error: 'Nomor telepon minimal 8 digit angka. Contoh: 081234567890' };
      }
      return { valid: true, value: cleanPhone };
    }
    case 'number': {
      const num = Number(str.replace(/[^0-9.-]/g, ''));
      if (isNaN(num)) {
        return { valid: false, error: 'Harap masukkan format angka yang valid.' };
      }
      return { valid: true, value: num };
    }
    case 'date': {
      return { valid: true, value: str };
    }
    case 'option': {
      return { valid: true, value: str };
    }
    case 'text':
    default:
      return { valid: true, value: str };
  }
}

// =========================================================================
// API ROUTES
// =========================================================================

// 1. Specialized Agents CRUD
app.get('/api/agents', (req, res) => {
  const agents = loadJSON(AGENTS_FILE, []);
  res.json(agents);
});

app.post('/api/agents', (req, res) => {
  const agents = loadJSON(AGENTS_FILE, []);
  const newAgent = {
    id: 'agent-' + Date.now(),
    name: req.body.name || 'Agent Baru',
    slug: req.body.slug || 'agent-' + Date.now(),
    department: req.body.department || 'Pemerintah Kabupaten Sleman',
    description: req.body.description || '',
    trigger: req.body.trigger || '',
    fallback_keywords: Array.isArray(req.body.fallback_keywords) ? req.body.fallback_keywords : [],
    persona: req.body.persona || 'professional',
    enabled: req.body.enabled !== false,
    steps: req.body.steps || []
  };
  agents.push(newAgent);
  saveJSON(AGENTS_FILE, agents);
  logActivity('SUCCESS', 'AGENT', `Specialized Agent "${newAgent.name}" berhasil dibuat`);
  res.status(201).json(newAgent);
});

app.put('/api/agents/:id', (req, res) => {
  const agents = loadJSON(AGENTS_FILE, []);
  const index = agents.findIndex(a => a.id === req.params.id);
  if (index === -1) return res.status(404).json({ error: 'Agent tidak ditemukan' });

  agents[index] = { ...agents[index], ...req.body };
  saveJSON(AGENTS_FILE, agents);
  logActivity('INFO', 'AGENT', `Specialized Agent "${agents[index].name}" diperbarui`);
  res.json(agents[index]);
});

app.delete('/api/agents/:id', (req, res) => {
  let agents = loadJSON(AGENTS_FILE, []);
  const target = agents.find(a => a.id === req.params.id);
  agents = agents.filter(a => a.id !== req.params.id);
  saveJSON(AGENTS_FILE, agents);
  if (target) {
    logActivity('WARN', 'AGENT', `Specialized Agent "${target.name}" telah dihapus`);
  }
  res.json({ success: true });
});

// 2. API Connectors CRUD
app.get('/api/connectors', (req, res) => {
  const connectors = loadJSON(CONNECTORS_FILE, []);
  res.json(connectors);
});

app.post('/api/connectors', (req, res) => {
  const connectors = loadJSON(CONNECTORS_FILE, []);
  const newConnector = {
    id: 'conn-' + Date.now(),
    name: req.body.name || 'Connector Baru',
    url: req.body.url || 'https://api.example.com',
    method: req.body.method || 'POST',
    auth_type: req.body.auth_type || 'Bearer Token',
    auth_token: req.body.auth_token || '',
    param_mapping: req.body.param_mapping || [],
    response_mapping: req.body.response_mapping || [],
    mock_response: req.body.mock_response || { status: 'success', data: { id: 'MOCK-123' } }
  };
  connectors.push(newConnector);
  saveJSON(CONNECTORS_FILE, connectors);
  logActivity('SUCCESS', 'WORKFLOW', `API Connector "${newConnector.name}" ditambahkan`);
  res.status(201).json(newConnector);
});

app.post('/api/connectors/test', (req, res) => {
  const { id } = req.body;
  const connectors = loadJSON(CONNECTORS_FILE, []);
  const conn = connectors.find(c => c.id === id);
  if (!conn) return res.status(404).json({ error: 'Connector tidak ditemukan' });

  // Simulate instant healthy response
  res.json({
    status: 'connected',
    statusCode: 200,
    latency: '42ms',
    data: conn.mock_response || { message: 'Connector test response OK' }
  });
});

// 3. Conversation States / Sessions
app.get('/api/sessions', (req, res) => {
  const list = Array.from(sessions.values()).sort((a, b) => new Date(b.updatedAt) - new Date(a.updatedAt));
  res.json(list);
});

app.get('/api/sessions/:id', (req, res) => {
  const session = sessions.get(req.params.id);
  if (!session) return res.status(404).json({ error: 'Session tidak ditemukan' });
  res.json(session);
});

app.post('/api/sessions/:id/takeover', (req, res) => {
  const { message, operatorName = 'Petugas CS Sleman' } = req.body;
  const session = sessions.get(req.params.id);
  if (!session) return res.status(404).json({ error: 'Session tidak ditemukan' });

  session.status = 'operator_takeover';
  const humanMsg = {
    sender: 'operator',
    name: operatorName,
    text: message,
    timestamp: new Date().toISOString()
  };
  session.history.push(humanMsg);
  session.updatedAt = new Date().toISOString();

  logActivity('INFO', 'INBOX', `Operator mengambil alih percakapan sesi ${session.id}: "${message}"`);
  res.json({ success: true, session });
});

app.post('/api/sessions/:id/reset', (req, res) => {
  sessions.delete(req.params.id);
  logActivity('INFO', 'SESSION', `Sesi ${req.params.id} direset`);
  res.json({ success: true });
});

// 4. Activity Logs & Stats
app.get('/api/logs', (req, res) => {
  res.json(activityLogs);
});

app.get('/api/stats', (req, res) => {
  const agents = loadJSON(AGENTS_FILE, []);
  const connectors = loadJSON(CONNECTORS_FILE, []);
  const sessionList = Array.from(sessions.values());
  const totalMessages = sessionList.reduce((acc, s) => acc + (s.history ? s.history.length : 0), 0);

  res.json({
    totalAgents: agents.length,
    activeAgents: agents.filter(a => a.enabled).length,
    totalConnectors: connectors.length,
    activeSessions: sessions.size,
    totalMessagesProcessed: totalMessages + 24, // demo baseline
    satisfactionRate: '98.4%',
    avgResponseTime: '0.4s'
  });
});

// 5. RAGFlow Configuration
app.get('/api/ragflow/status', (req, res) => {
  res.json({
    ...ragflowConfig,
    isConnected: ragflowConfig.isLive,
    engine: 'Infinity + Ollama Llama 3.2',
    mode: 'Multi-Tenant Air-Gapped'
  });
});

app.post('/api/ragflow/config', (req, res) => {
  ragflowConfig = { ...ragflowConfig, ...req.body };
  logActivity('INFO', 'RAGFLOW', 'Konfigurasi RAGFlow berhasil disimpan');
  res.json({ success: true, config: ragflowConfig });
});

// =========================================================================
// CORE CHAT EXECUTION ENGINE (State Machine & Step Executor)
// =========================================================================
app.post('/api/chat', async (req, res) => {
  const { message, sessionId = 'sess-' + Date.now(), agentSlug, tenantId = 'sleman-default' } = req.body;
  const agents = loadJSON(AGENTS_FILE, []);
  const connectors = loadJSON(CONNECTORS_FILE, []);
  const knowledge = loadJSON(KNOWLEDGE_FILE, []);

  // Retrieve or create session
  let session = sessions.get(sessionId);
  if (!session) {
    session = {
      id: sessionId,
      agentId: null,
      agentSlug: null,
      agentName: null,
      department: 'Pemerintah Kabupaten Sleman',
      currentStepIndex: -1,
      variables: {},
      history: [],
      status: 'active',
      stepHistory: [],
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    };
    sessions.set(sessionId, session);
  }

  // Push user message to history
  session.history.push({
    sender: 'user',
    text: message,
    timestamp: new Date().toISOString()
  });
  session.updatedAt = new Date().toISOString();

  // If operator took over, do not auto-respond
  if (session.status === 'operator_takeover') {
    return res.json({
      reply: 'Pesan Anda telah diterima oleh petugas operator kami. Mohon menunggu sejenak.',
      is_operator: true,
      debug: { status: 'operator_active' }
    });
  }

  let activeAgent = session.agentId ? agents.find(a => a.id === session.agentId) : null;
  let routingInfo = null;

  // -------------------------------------------------------------
  // A. ROUTING PHASE (if no agent in progress or reset requested)
  // -------------------------------------------------------------
  const isExitCommand = ['batal', 'cancel', 'reset', 'kembali', 'menu utama', 'exit'].includes(
    (message || '').toLowerCase().trim()
  );

  if (isExitCommand && activeAgent) {
    session.agentId = null;
    session.agentSlug = null;
    session.currentStepIndex = -1;
    session.variables = {};
    activeAgent = null;

    const replyMsg = 'Baik, percakapan telah direset. Ada hal lain seputar layanan Pemkab Sleman yang bisa saya bantu?';
    session.history.push({ sender: 'bot', text: replyMsg, timestamp: new Date().toISOString() });
    return res.json({
      reply: replyMsg,
      quick_replies: ['Layanan Dukcapil Sleman', 'Wisata Sleman', 'Lapor Aduan Warga', 'Visi Misi Sleman'],
      debug: {
        routing: { agent_name: 'Super Agent 🧠', confidence: 1.0, is_specialized: false },
        current_step: 'IDLE',
        collected_variables: session.variables,
        step_history: session.stepHistory
      }
    });
  }

  // If no active agent currently executing steps
  if (!activeAgent || session.currentStepIndex === -1) {
    routingInfo = routeIntent(message, agents, agentSlug);

    if (routingInfo.agent) {
      activeAgent = routingInfo.agent;
      session.agentId = activeAgent.id;
      session.agentSlug = activeAgent.slug;
      session.agentName = activeAgent.name;
      session.department = activeAgent.department;
      session.currentStepIndex = 0; // Start at step 0
      session.stepHistory.push({ step: 0, title: activeAgent.steps[0]?.title || 'Init', agent: activeAgent.slug });
      logActivity('INFO', 'ROUTING', `Intent routed to [${activeAgent.name}] with confidence ${routingInfo.confidence}`);
    } else {
      // Routed to Super Agent 🧠 (RAGFlow Knowledge Base Fallback)
      logActivity('INFO', 'ROUTING', `Query handled by Super Agent 🧠: "${message}"`);
      const matchedItem = knowledge.find(k =>
        k.keywords.some(kw => (message || '').toLowerCase().includes(kw))
      );

      let superReply = '';
      if (matchedItem) {
        superReply = `**${matchedItem.topic}**\n\n${matchedItem.content}\n\n*Sumber: ${matchedItem.department}*`;
      } else {
        superReply =
          'Halo! Saya **S-Bot Super Agent AI Pemkab Sleman** 🧠. Anda dapat menanyakan seputar regulasi kependudukan (Dukcapil), rekomendasi wisata Sleman, serta pengaduan masyarakat. Silakan pilih salah satu layanan spesifik atau ajukan pertanyaan Anda:';
      }

      const quickReplies = ['Syarat KTP Baru', 'Pariwisata & Jeep Merapi', 'Aduan Wifi Padukuhan', 'Visi Misi Sleman'];
      session.history.push({ sender: 'bot', text: superReply, timestamp: new Date().toISOString() });

      return res.json({
        reply: superReply,
        quick_replies: quickReplies,
        step_type: 'knowledge_response',
        debug: {
          routing: { agent_name: 'S-Bot Super Agent 🧠', confidence: 0.95, is_specialized: false },
          current_step: 'Knowledge Base / RAG',
          step_title: matchedItem ? matchedItem.topic : 'General Inquiry',
          collected_variables: session.variables,
          step_history: session.stepHistory
        }
      });
    }
  }

  // -------------------------------------------------------------
  // B. STEP EXECUTION PHASE (Specialized Agent Flow)
  // -------------------------------------------------------------
  let steps = activeAgent.steps || [];
  let currentStep = steps[session.currentStepIndex];

  // If user is currently responding to a 'collect_input' step
  if (currentStep && currentStep.type === 'collect_input') {
    const valResult = validateInput(currentStep.input_type, message, currentStep.options);
    if (!valResult.valid) {
      logActivity('WARN', 'INPUT', `Validasi gagal pada step "${currentStep.title}": ${valResult.error}`);
      return res.json({
        reply: `⚠️ ${valResult.error}\n\n${currentStep.prompt}`,
        quick_replies: currentStep.options || [],
        step_type: 'collect_input_retry',
        input_type: currentStep.input_type,
        debug: {
          routing: { agent_name: activeAgent.name, agent_slug: activeAgent.slug, confidence: 0.99, is_specialized: true },
          current_step: `Step ${session.currentStepIndex + 1} (${currentStep.title}) [RETRY]`,
          step_title: currentStep.title,
          collected_variables: session.variables,
          step_history: session.stepHistory
        }
      });
    }

    // Save valid input to variables
    session.variables[currentStep.variable_name] = valResult.value;
    logActivity('INFO', 'INPUT', `Variable "${currentStep.variable_name}" = "${valResult.value}"`);

    // Advance to next step
    session.currentStepIndex++;
    currentStep = steps[session.currentStepIndex];
  }

  // Execute non-input steps (Workflow, Condition, Prompt, Response)
  while (currentStep) {
    // 1. Condition Step
    if (currentStep.type === 'condition') {
      const varVal = session.variables[currentStep.variable];
      let conditionMet = false;
      if (currentStep.operator === '==') conditionMet = String(varVal) === String(currentStep.value);
      else if (currentStep.operator === '!=') conditionMet = String(varVal) !== String(currentStep.value);
      else if (currentStep.operator === 'contains') conditionMet = String(varVal).includes(String(currentStep.value));

      session.stepHistory.push({ step: session.currentStepIndex, title: currentStep.title, result: conditionMet });
      session.currentStepIndex = conditionMet ? currentStep.true_step_index : currentStep.false_step_index;
      currentStep = steps[session.currentStepIndex];
      continue;
    }

    // 2. Workflow Step (Call API connector / backend process)
    if (currentStep.type === 'workflow') {
      const conn = connectors.find(c => c.id === currentStep.connector_id);
      logActivity('INFO', 'WORKFLOW', `Menjalankan workflow "${currentStep.title}" via connector: ${conn ? conn.name : 'Internal'}`);

      // Map mock response variables
      if (conn && conn.response_mapping) {
        for (const map of conn.response_mapping) {
          // Simulate extracted value from path
          const randomSuffix = Math.floor(1000 + Math.random() * 9000);
          if (map.variable_name === 'ticket_code') session.variables['ticket_code'] = `SLM-${randomSuffix}`;
          else if (map.variable_name === 'booking_ref') session.variables['booking_ref'] = `WISATA-${randomSuffix}`;
          else if (map.variable_name === 'ticket_id') session.variables['ticket_id'] = `${randomSuffix}`;
          else session.variables[map.variable_name] = `RES-${randomSuffix}`;
        }
      } else {
        session.variables['booking_id'] = `ID-${Math.floor(1000 + Math.random() * 9000)}`;
      }

      session.stepHistory.push({ step: session.currentStepIndex, title: currentStep.title, type: 'workflow' });
      session.currentStepIndex++;
      currentStep = steps[session.currentStepIndex];
      continue;
    }

    // 3. Prompt Step (Bot speaks and offers quick replies, then moves to next step)
    if (currentStep.type === 'prompt') {
      const rawPrompt = replaceVariables(currentStep.prompt, session.variables);
      session.stepHistory.push({ step: session.currentStepIndex, title: currentStep.title });
      session.history.push({ sender: 'bot', text: rawPrompt, timestamp: new Date().toISOString() });

      // Move forward so next turn awaits the subsequent step
      session.currentStepIndex++;
      const nextStep = steps[session.currentStepIndex];

      return res.json({
        reply: rawPrompt,
        quick_replies: currentStep.quick_replies || (nextStep && nextStep.options) || [],
        step_type: 'prompt',
        input_type: nextStep && nextStep.type === 'collect_input' ? nextStep.input_type : 'text',
        debug: {
          routing: { agent_name: activeAgent.name, agent_slug: activeAgent.slug, confidence: 0.99, is_specialized: true },
          current_step: `Step ${session.currentStepIndex} (${currentStep.title})`,
          step_title: currentStep.title,
          collected_variables: session.variables,
          step_history: session.stepHistory
        }
      });
    }

    // 4. Collect Input Step (Waiting for user input)
    if (currentStep.type === 'collect_input') {
      const rawPrompt = replaceVariables(currentStep.prompt, session.variables);
      session.stepHistory.push({ step: session.currentStepIndex, title: currentStep.title });
      session.history.push({ sender: 'bot', text: rawPrompt, timestamp: new Date().toISOString() });

      return res.json({
        reply: rawPrompt,
        quick_replies: currentStep.options || [],
        step_type: 'collect_input',
        input_type: currentStep.input_type,
        options: currentStep.options || null,
        debug: {
          routing: { agent_name: activeAgent.name, agent_slug: activeAgent.slug, confidence: 0.99, is_specialized: true },
          current_step: `Step ${session.currentStepIndex + 1} (${currentStep.title})`,
          step_title: currentStep.title,
          collected_variables: session.variables,
          step_history: session.stepHistory
        }
      });
    }

    // 5. Response Step (Final message)
    if (currentStep.type === 'response') {
      const finalMsg = replaceVariables(currentStep.message, session.variables);
      session.stepHistory.push({ step: session.currentStepIndex, title: currentStep.title, final: true });
      session.history.push({ sender: 'bot', text: finalMsg, timestamp: new Date().toISOString() });

      // Reset agent state after completion
      const completedAgentName = activeAgent.name;
      session.agentId = null;
      session.agentSlug = null;
      session.currentStepIndex = -1;

      return res.json({
        reply: finalMsg,
        quick_replies: ['Urus Layanan Lain', 'Tanya Seputar Sleman', 'Selesai'],
        step_type: 'response_final',
        debug: {
          routing: { agent_name: completedAgentName, confidence: 1.0, is_specialized: true },
          current_step: 'Completed (Final Response)',
          step_title: currentStep.title,
          collected_variables: session.variables,
          step_history: session.stepHistory
        }
      });
    }

    break;
  }

  // Fallback if flow runs out of steps
  session.agentId = null;
  session.currentStepIndex = -1;
  const finishMsg = 'Alur percakapan telah selesai. Apakah ada hal lain yang bisa kami bantu?';
  res.json({
    reply: finishMsg,
    quick_replies: ['Layanan Dukcapil', 'Wisata Sleman', 'Pengaduan'],
    debug: {
      routing: { agent_name: 'S-Bot Super Agent 🧠', confidence: 1.0, is_specialized: false },
      current_step: 'END',
      collected_variables: session.variables,
      step_history: session.stepHistory
    }
  });
});

// Alias for sbot-widget.js and sbot-widget.css
app.get('/widget/dist/sbot-widget.js', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'widget', 'dist', 'nyai-widget.js'));
});
app.get('/widget/dist/sbot-widget.css', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'widget', 'dist', 'nyai-widget.css'));
});

// Serve Admin Panel as default
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Serve Demo Portal (Sleman Citizen Portal)
app.get('/demo', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'demo.html'));
});

app.listen(PORT, () => {
  console.log(`====================================================`);
  console.log(`🚀 S-Bot Agentic AI Platform running on http://localhost:${PORT}`);
  console.log(`📊 S-Bot Studio & Test Console: http://localhost:${PORT}`);
  console.log(`🏛️ Demo Portal Pemkab Sleman:  http://localhost:${PORT}/demo`);
  console.log(`🧩 Fullscreen Widget:          http://localhost:${PORT}/widget/fullscreen.html`);
  console.log(`====================================================`);
});
