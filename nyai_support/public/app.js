/**
 * S-Bot Agentic AI - Admin Console & Studio Frontend Controller
 */

// Global State
let currentTab = 'tab-dashboard';
let agentsData = [];
let connectorsData = [];
let testSessionId = 'console-sess-' + Date.now();
let selectedInboxSessionId = null;

// Audio Pop Effect (Web Audio API)
function playTone(freq = 600, duration = 0.08) {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.frequency.setValueAtTime(freq, ctx.currentTime);
    gain.gain.setValueAtTime(0.04, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.start();
    osc.stop(ctx.currentTime + duration);
  } catch (e) {}
}

document.addEventListener('DOMContentLoaded', () => {
  initNavigation();
  loadAllData();
  setupConsoleChat();
  updateEmbedCodeSnippets();

  // Periodic polling for sessions and logs
  setInterval(() => {
    if (currentTab === 'tab-sessions') loadSessions();
    if (currentTab === 'tab-logs') loadLogs();
    if (currentTab === 'tab-dashboard') loadStats();
  }, 4000);
});

// =========================================================================
// 1. NAVIGATION & TABS
// =========================================================================
function initNavigation() {
  const navItems = document.querySelectorAll('.sidebar-menu .nav-item');
  navItems.forEach(item => {
    item.addEventListener('click', () => {
      const tabId = item.getAttribute('data-tab');
      if (!tabId) return;

      // Update Nav active
      navItems.forEach(n => n.classList.remove('active'));
      item.classList.add('active');

      // Update Panels
      document.querySelectorAll('.tab-content').forEach(p => p.classList.remove('active'));
      const activePanel = document.getElementById(tabId);
      if (activePanel) activePanel.classList.add('active');

      currentTab = tabId;

      // Update title
      const titleEl = document.getElementById('current-page-title');
      const subEl = document.getElementById('current-page-sub');
      const tabName = item.querySelector('span:nth-child(2)').textContent;
      titleEl.textContent = tabName;

      // Refresh specific tab data
      if (tabId === 'tab-agents') renderAgentsList();
      if (tabId === 'tab-connectors') renderConnectorsTable();
      if (tabId === 'tab-sessions') loadSessions();
      if (tabId === 'tab-logs') loadLogs();
      if (tabId === 'tab-dashboard') loadStats();
      if (tabId === 'tab-embed') updateEmbedCodeSnippets();
    });
  });
}

// =========================================================================
// 2. DATA LOADING & STATS
// =========================================================================
async function loadAllData() {
  try {
    const [resAgents, resConn, resStats, resLogs] = await Promise.all([
      fetch('/api/agents'),
      fetch('/api/connectors'),
      fetch('/api/stats'),
      fetch('/api/logs')
    ]);

    agentsData = await resAgents.json();
    connectorsData = await resConn.json();
    const stats = await resStats.json();
    const logs = await resLogs.json();

    updateDashboard(stats, logs);
    renderAgentsList();
    renderConnectorsTable();
    populateConsoleAgentSelect();
  } catch (err) {
    console.error('Error loading data:', err);
  }
}

async function loadStats() {
  try {
    const res = await fetch('/api/stats');
    const stats = await res.json();
    document.getElementById('stat-total-agents').textContent = stats.totalAgents || '3';
    document.getElementById('stat-total-connectors').textContent = stats.totalConnectors || '3';
    document.getElementById('stat-active-sessions').textContent = stats.activeSessions || '0';
    document.getElementById('badge-session-count').textContent = stats.activeSessions || '0';
  } catch (e) {}
}

function updateDashboard(stats, logs) {
  document.getElementById('stat-total-agents').textContent = stats.totalAgents || agentsData.length;
  document.getElementById('stat-total-connectors').textContent = stats.totalConnectors || connectorsData.length;
  document.getElementById('stat-active-sessions').textContent = stats.activeSessions || '0';
  document.getElementById('stat-satisfaction').textContent = stats.satisfactionRate || '98.4%';
  document.getElementById('badge-agent-count').textContent = agentsData.length;

  // Render recent logs in dashboard
  const logContainer = document.getElementById('dashboard-recent-logs');
  if (logContainer && Array.isArray(logs)) {
    logContainer.innerHTML = logs.slice(0, 5).map(l => `
      <div style="display:flex; gap:12px; padding:6px 0; border-bottom:1px solid #f1f5f9;">
        <span style="color:#64748b;">${new Date(l.timestamp).toLocaleTimeString()}</span>
        <span style="font-weight:700; color:${l.level === 'SUCCESS' ? '#059669' : l.level === 'WARN' ? '#d97706' : '#2563eb'};">[${l.category}]</span>
        <span style="color:#1e293b; flex:1;">${l.message}</span>
      </div>
    `).join('');
  }
}

// =========================================================================
// 3. SPECIALIZED AGENTS MANAGEMENT
// =========================================================================
function renderAgentsList() {
  const container = document.getElementById('agents-list-container');
  if (!container) return;

  const personaIcons = {
    professional: '👔',
    friendly: '😊',
    witty: '🎭',
    empathetic: '💚'
  };

  container.innerHTML = agentsData.map(agent => `
    <div class="agent-card">
      <div>
        <div class="agent-top">
          <div class="agent-avatar-icon">${personaIcons[agent.persona] || '🎯'}</div>
          <div class="agent-info">
            <h4>${agent.name}</h4>
            <span class="agent-slug">${agent.slug}</span>
          </div>
        </div>
        <p class="agent-desc">${agent.description || 'Tidak ada deskripsi'}</p>
        <div class="agent-meta-tags">
          <span class="tag-persona">${personaIcons[agent.persona] || ''} Persona: ${agent.persona}</span>
          <span class="tag-step-count">⚡ ${agent.steps ? agent.steps.length : 0} Steps Flow</span>
          <span class="tag-persona" style="background:${agent.enabled ? '#ecfdf5' : '#fef2f2'}; color:${agent.enabled ? '#047857' : '#991b1b'};">
            ${agent.enabled ? '● Enabled' : '○ Disabled'}
          </span>
        </div>
      </div>
      <div class="agent-actions">
        <button class="btn-secondary" style="font-size:12px;" onclick="editAgent('${agent.id}')">✏️ Edit Flow</button>
        <button class="btn-primary" style="font-size:12px;" onclick="testSpecificAgent('${agent.slug}')">🧪 Test Flow</button>
      </div>
    </div>
  `).join('');
}

function selectPersona(persona) {
  document.querySelectorAll('.persona-card').forEach(c => {
    c.classList.toggle('selected', c.getAttribute('data-persona') === persona);
  });
  document.getElementById('agent-form-persona').value = persona;
}

// Modal open/close
function openNewAgentModal() {
  document.getElementById('modal-agent-title').textContent = 'Buat Specialized Agent Baru';
  document.getElementById('agent-form-id').value = '';
  document.getElementById('agent-form-name').value = '';
  document.getElementById('agent-form-slug').value = '';
  document.getElementById('agent-form-dept').value = 'Pemerintah Kabupaten Sleman';
  document.getElementById('agent-form-desc').value = '';
  document.getElementById('agent-form-trigger').value = '';
  document.getElementById('agent-form-keywords').value = '';
  selectPersona('professional');

  // Default initial steps
  const stepsList = document.getElementById('modal-steps-list');
  stepsList.innerHTML = '';
  addStepItem('prompt', 'Sambut Pengguna', 'Halo! Bagaimana saya dapat membantu Anda?');
  addStepItem('collect_input', 'Kumpulkan Nama', 'Mohon tuliskan nama lengkap Anda:');

  document.getElementById('modal-agent').classList.add('open');
}

function closeAgentModal() {
  document.getElementById('modal-agent').classList.remove('open');
}

function addStepItem(type = 'prompt', title = 'Langkah Baru', content = '') {
  const stepsList = document.getElementById('modal-steps-list');
  const index = stepsList.children.length;
  const div = document.createElement('div');
  div.className = 'step-item';
  div.innerHTML = `
    <span class="step-badge ${type}">${type}</span>
    <div class="step-details">
      <input type="text" class="form-control" style="font-size:13px; font-weight:700; margin-bottom:4px; padding:4px 8px;" value="${title}" placeholder="Judul Step">
      <input type="text" class="form-control" style="font-size:12px; padding:4px 8px;" value="${content}" placeholder="Teks / Prompt / Config">
    </div>
    <select class="form-control" style="width: auto; font-size:12px; padding:4px 8px;" onchange="this.parentElement.querySelector('.step-badge').className='step-badge '+this.value; this.parentElement.querySelector('.step-badge').textContent=this.value;">
      <option value="prompt" ${type === 'prompt' ? 'selected' : ''}>Prompt</option>
      <option value="collect_input" ${type === 'collect_input' ? 'selected' : ''}>Collect Input</option>
      <option value="workflow" ${type === 'workflow' ? 'selected' : ''}>Workflow</option>
      <option value="response" ${type === 'response' ? 'selected' : ''}>Response</option>
    </select>
    <button type="button" class="btn-secondary" style="padding:4px 8px; color:#ef4444;" onclick="this.parentElement.remove()">✕</button>
  `;
  stepsList.appendChild(div);
}

function editAgent(agentId) {
  const agent = agentsData.find(a => a.id === agentId);
  if (!agent) return;

  document.getElementById('modal-agent-title').textContent = `Edit Agent: ${agent.name}`;
  document.getElementById('agent-form-id').value = agent.id;
  document.getElementById('agent-form-name').value = agent.name;
  document.getElementById('agent-form-slug').value = agent.slug;
  document.getElementById('agent-form-dept').value = agent.department || '';
  document.getElementById('agent-form-desc').value = agent.description || '';
  document.getElementById('agent-form-trigger').value = agent.trigger || '';
  document.getElementById('agent-form-keywords').value = (agent.fallback_keywords || []).join(', ');
  selectPersona(agent.persona || 'professional');

  const stepsList = document.getElementById('modal-steps-list');
  stepsList.innerHTML = '';
  if (Array.isArray(agent.steps)) {
    agent.steps.forEach(step => {
      addStepItem(step.type, step.title || step.id, step.prompt || step.message || '');
    });
  }

  document.getElementById('modal-agent').classList.add('open');
}

async function saveAgentFromModal() {
  const id = document.getElementById('agent-form-id').value;
  const name = document.getElementById('agent-form-name').value.trim();
  const slug = document.getElementById('agent-form-slug').value.trim();
  const department = document.getElementById('agent-form-dept').value.trim();
  const description = document.getElementById('agent-form-desc').value.trim();
  const trigger = document.getElementById('agent-form-trigger').value.trim();
  const keywordsStr = document.getElementById('agent-form-keywords').value.trim();
  const persona = document.getElementById('agent-form-persona').value;

  if (!name || !slug) {
    alert('Nama Agent dan Slug wajib diisi!');
    return;
  }

  // Parse steps from UI
  const stepItems = document.querySelectorAll('#modal-steps-list .step-item');
  const steps = Array.from(stepItems).map((el, i) => {
    const inputs = el.querySelectorAll('input');
    const select = el.querySelector('select');
    const type = select.value;
    const title = inputs[0].value.trim();
    const textVal = inputs[1].value.trim();

    if (type === 'collect_input') {
      return {
        id: 'step-' + (i + 1),
        type: 'collect_input',
        title: title || `Step ${i + 1}`,
        variable_name: 'input_var_' + (i + 1),
        input_type: 'text',
        prompt: textVal || 'Mohon masukkan data:'
      };
    } else if (type === 'workflow') {
      return {
        id: 'step-' + (i + 1),
        type: 'workflow',
        title: title || `Step ${i + 1}`,
        connector_id: 'conn-ticket-system',
        action_name: 'execute'
      };
    } else if (type === 'response') {
      return {
        id: 'step-' + (i + 1),
        type: 'response',
        title: title || `Step ${i + 1}`,
        message: textVal || 'Terima kasih, data Anda telah tercatat.'
      };
    } else {
      return {
        id: 'step-' + (i + 1),
        type: 'prompt',
        title: title || `Step ${i + 1}`,
        prompt: textVal || 'Halo!'
      };
    }
  });

  const payload = {
    name,
    slug,
    department,
    description,
    trigger,
    fallback_keywords: keywordsStr ? keywordsStr.split(',').map(s => s.trim()) : [],
    persona,
    enabled: true,
    steps
  };

  try {
    const method = id ? 'PUT' : 'POST';
    const url = id ? `/api/agents/${id}` : '/api/agents';
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    if (res.ok) {
      closeAgentModal();
      await loadAllData();
      alert('Agent berhasil disimpan!');
    } else {
      alert('Gagal menyimpan agent.');
    }
  } catch (err) {
    console.error(err);
    alert('Terjadi kesalahan jaringan.');
  }
}

// =========================================================================
// 4. TEST CONSOLE & REAL-TIME DEBUG PANEL
// =========================================================================
function populateConsoleAgentSelect() {
  const select = document.getElementById('console-target-agent');
  if (!select) return;
  select.innerHTML = '<option value="">🔀 Auto Route (Super Agent)</option>';
  agentsData.forEach(a => {
    const opt = document.createElement('option');
    opt.value = a.slug;
    opt.textContent = `🎯 ${a.name} (${a.slug})`;
    select.appendChild(opt);
  });
}

function testSpecificAgent(slug) {
  // Switch to Test Console tab
  document.querySelector('.sidebar-menu [data-tab="tab-test-console"]').click();
  const select = document.getElementById('console-target-agent');
  if (select) select.value = slug;
  resetTestSession();
  sendConsoleMessage('halo');
}

function setupConsoleChat() {
  const form = document.getElementById('console-chat-form');
  const input = document.getElementById('console-chat-input');
  const resetBtn = document.getElementById('console-reset-btn');

  if (form && input) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      sendConsoleMessage();
    });
  }

  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      resetTestSession();
    });
  }
}

function resetTestSession() {
  testSessionId = 'console-sess-' + Date.now();
  const messagesEl = document.getElementById('console-messages');
  messagesEl.innerHTML = '';
  appendConsoleBubble('bot', 'Sesi telah direset. Silakan ketik pesan atau pilih target agent di atas.');

  // Reset debug panel
  document.getElementById('dbg-agent-name').textContent = 'S-Bot Super Agent 🧠';
  document.getElementById('dbg-confidence').textContent = 'Confidence: 0.95';
  document.getElementById('dbg-route-type').textContent = 'Type: Idle / General Knowledge';
  document.getElementById('dbg-current-step').textContent = 'Step: IDLE (Menunggu Input)';
  document.getElementById('dbg-variables-view').textContent = '{}';
  document.getElementById('dbg-step-history').innerHTML = '<div style="color:#94a3b8; font-style:italic;">Sesi baru dimulai.</div>';
}

function appendConsoleBubble(sender, text, quickReplies = []) {
  const messagesEl = document.getElementById('console-messages');
  const group = document.createElement('div');
  group.className = `nyai-msg-group ${sender}`;

  const bubble = document.createElement('div');
  bubble.className = `nyai-bubble ${sender}`;
  bubble.innerHTML = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
  group.appendChild(bubble);

  if (quickReplies && quickReplies.length > 0) {
    const qrWrap = document.createElement('div');
    qrWrap.className = 'nyai-quick-replies';
    quickReplies.forEach(qr => {
      const chip = document.createElement('button');
      chip.type = 'button';
      chip.className = 'nyai-chip';
      chip.textContent = qr;
      chip.onclick = () => {
        playTone(650);
        sendConsoleMessage(qr);
      };
      qrWrap.appendChild(chip);
    });
    group.appendChild(qrWrap);
  }

  messagesEl.appendChild(group);
  messagesEl.scrollTop = messagesEl.scrollHeight;

  if (sender === 'bot') playTone(800);
}

async function sendConsoleMessage(textToSend = null) {
  const input = document.getElementById('console-chat-input');
  const message = (textToSend || input.value || '').trim();
  if (!message) return;

  input.value = '';
  appendConsoleBubble('user', message);

  const targetSlug = document.getElementById('console-target-agent').value || null;

  try {
    const res = await fetch('/api/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        message,
        sessionId: testSessionId,
        agentSlug: targetSlug
      })
    });

    const data = await res.json();
    if (data.reply) {
      appendConsoleBubble('bot', data.reply, data.quick_replies);
    }

    // UPDATE REAL-TIME DEBUG PANEL
    updateDebugPanel(data.debug);

  } catch (err) {
    console.error(err);
    appendConsoleBubble('bot', '⚠️ Error contacting AI engine.');
  }
}

function updateDebugPanel(debug) {
  if (!debug) return;

  // 1. Routing
  if (debug.routing) {
    document.getElementById('dbg-agent-name').textContent = debug.routing.agent_name || 'Super Agent 🧠';
    document.getElementById('dbg-confidence').textContent = `Confidence: ${(debug.routing.confidence * 100).toFixed(0)}%`;
    document.getElementById('dbg-route-type').textContent = debug.routing.is_specialized
      ? 'Type: 🎯 Specialized Workflow Agent'
      : 'Type: 🧠 Super Agent (Knowledge Base & RAG)';
  }

  // 2. Current Step
  if (debug.current_step) {
    document.getElementById('dbg-current-step').textContent = `${debug.current_step} - ${debug.step_title || ''}`;
  }

  // 3. Collected Variables
  if (debug.collected_variables) {
    document.getElementById('dbg-variables-view').textContent = JSON.stringify(debug.collected_variables, null, 2);
  }

  // 4. Step History
  if (Array.isArray(debug.step_history)) {
    const historyEl = document.getElementById('dbg-step-history');
    if (debug.step_history.length > 0) {
      historyEl.innerHTML = debug.step_history.map(s => `
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:6px 10px; display:flex; justify-content:space-between;">
          <span style="font-weight:600; color:#0f172a;">${s.title || 'Step ' + s.step}</span>
          <span style="color:#059669; font-weight:700;">OK ✓</span>
        </div>
      `).join('');
    }
  }
}

// =========================================================================
// 5. API CONNECTORS
// =========================================================================
function renderConnectorsTable() {
  const tbody = document.getElementById('connectors-table-body');
  if (!tbody) return;

  tbody.innerHTML = connectorsData.map(c => `
    <tr>
      <td><strong>${c.name}</strong></td>
      <td><code style="font-size:12px; background:#f1f5f9; padding:2px 6px; border-radius:4px;">${c.url}</code></td>
      <td><span style="background:#eff6ff; color:#1d4ed8; font-weight:700; font-size:11px; padding:2px 6px; border-radius:4px;">${c.method}</span></td>
      <td>${c.auth_type}</td>
      <td>${(c.param_mapping || []).map(p => `<span style="font-size:11px; background:#f8fafc; border:1px solid #e2e8f0; padding:2px 4px; border-radius:4px; margin-right:4px;">${p.api_param} ➔ ${p.convo_var}</span>`).join('')}</td>
      <td>
        <button class="btn-secondary" style="font-size:12px; padding:4px 8px;" onclick="testConnector('${c.id}')">🧪 Test Ping</button>
      </td>
    </tr>
  `).join('');
}

async function testConnector(id) {
  try {
    const res = await fetch('/api/connectors/test', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });
    const result = await res.json();
    alert(`Status Connector: ${result.status.toUpperCase()}\nHTTP Code: ${result.statusCode}\nLatency: ${result.latency}\nMock Response: ${JSON.stringify(result.data)}`);
  } catch (e) {
    alert('Gagal menguji connector.');
  }
}

function openNewConnectorModal() {
  const name = prompt('Nama API Connector:', 'Sistem Verifikasi Berkas Dukcapil');
  const url = prompt('Endpoint URL:', 'https://api.dukcapil.slemankab.go.id/v1/verify');
  if (!name || !url) return;

  fetch('/api/connectors', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name, url, method: 'POST', auth_type: 'Bearer Token' })
  }).then(() => loadAllData());
}

// =========================================================================
// 6. CONVERSATION STATES & LIVE INBOX
// =========================================================================
async function loadSessions() {
  try {
    const res = await fetch('/api/sessions');
    const sessions = await res.json();
    const container = document.getElementById('sessions-list');
    if (!container) return;

    if (sessions.length === 0) {
      container.innerHTML = '<div style="padding:20px; text-align:center; color:#94a3b8; font-size:13px;">Belum ada sesi percakapan aktif. Coba tes di Test Console atau Demo Portal.</div>';
      return;
    }

    container.innerHTML = sessions.map(s => `
      <div style="padding:14px 16px; border-bottom:1px solid #f1f5f9; cursor:pointer; background:${selectedInboxSessionId === s.id ? '#ecfdf5' : 'white'};" onclick="selectInboxSession('${s.id}')">
        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
          <span style="font-weight:700; font-size:13px;">${s.agentName || 'Super Agent'}</span>
          <span style="font-size:10px; color:#94a3b8;">${new Date(s.updatedAt).toLocaleTimeString()}</span>
        </div>
        <div style="font-size:12px; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
          ${s.history && s.history.length > 0 ? s.history[s.history.length - 1].text : 'Sesi baru'}
        </div>
        <div style="margin-top:6px; font-size:10px; font-weight:700; color:${s.status === 'operator_takeover' ? '#2563eb' : '#059669'};">
          ${s.status === 'operator_takeover' ? '👤 Operator Takeover' : '🤖 AI Managed'}
        </div>
      </div>
    `).join('');
  } catch (e) {}
}

async function selectInboxSession(sessionId) {
  selectedInboxSessionId = sessionId;
  loadSessions();

  try {
    const res = await fetch(`/api/sessions/${sessionId}`);
    const session = await res.json();

    document.getElementById('inbox-session-title').textContent = `Sesi ID: ${session.id} (${session.agentName || 'Super Agent'})`;
    document.getElementById('inbox-session-status').innerHTML = session.status === 'operator_takeover'
      ? '<span style="color:#2563eb; font-weight:700;">👤 Intervensi Operator Aktif</span>'
      : '<span style="color:#059669; font-weight:700;">🤖 AI Menjawab Otomatis</span>';

    const msgContainer = document.getElementById('inbox-messages');
    msgContainer.innerHTML = (session.history || []).map(m => `
      <div class="nyai-msg-group ${m.sender === 'user' ? 'user' : 'bot'}">
        <div class="nyai-bubble ${m.sender === 'user' ? 'user' : 'bot'}" style="${m.sender === 'operator' ? 'background:#2563eb; color:white;' : ''}">
          ${m.name ? `<strong>[${m.name}]</strong><br>` : ''}
          ${m.text}
        </div>
        <div class="nyai-time">${new Date(m.timestamp).toLocaleTimeString()}</div>
      </div>
    `).join('');
    msgContainer.scrollTop = msgContainer.scrollHeight;

    document.getElementById('inbox-takeover-box').style.display = 'block';
  } catch (e) {}
}

async function handleOperatorTakeover(e) {
  e.preventDefault();
  const input = document.getElementById('inbox-takeover-input');
  const message = input.value.trim();
  if (!message || !selectedInboxSessionId) return;

  try {
    await fetch(`/api/sessions/${selectedInboxSessionId}/takeover`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message, operatorName: 'Petugas CS Sleman' })
    });
    input.value = '';
    selectInboxSession(selectedInboxSessionId);
  } catch (e) {}
}

// =========================================================================
// 7. ACTIVITY LOGS
// =========================================================================
async function loadLogs() {
  try {
    const res = await fetch('/api/logs');
    const logs = await res.json();
    const tbody = document.getElementById('activity-logs-body');
    if (!tbody) return;

    tbody.innerHTML = logs.map(l => `
      <tr>
        <td style="font-family:'JetBrains Mono'; font-size:12px; color:#64748b;">${new Date(l.timestamp).toLocaleString()}</td>
        <td>
          <span style="font-size:11px; font-weight:700; padding:2px 6px; border-radius:4px; background:${l.level === 'SUCCESS' ? '#ecfdf5' : l.level === 'WARN' ? '#fffbeb' : '#eff6ff'}; color:${l.level === 'SUCCESS' ? '#047857' : l.level === 'WARN' ? '#b45309' : '#1e40af'};">
            ${l.level}
          </span>
        </td>
        <td><strong style="font-size:12px;">${l.category}</strong></td>
        <td>${l.message}</td>
      </tr>
    `).join('');
  } catch (e) {}
}

// =========================================================================
// 8. RAGFLOW & EMBED SNIPPETS
// =========================================================================
async function saveRagflowConfig(e) {
  e.preventDefault();
  const baseUrl = document.getElementById('rf-url').value;
  const apiKey = document.getElementById('rf-key').value;
  const datasetId = document.getElementById('rf-dataset').value;
  const similarityThreshold = parseFloat(document.getElementById('rf-threshold').value);

  try {
    const res = await fetch('/api/ragflow/config', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ baseUrl, apiKey, datasetId, similarityThreshold })
    });
    if (res.ok) alert('Konfigurasi RAGFlow berhasil disimpan!');
  } catch (e) {
    alert('Gagal menyimpan.');
  }
}

function updateEmbedCodeSnippets() {
  const origin = window.location.origin;
  const select = document.getElementById('embed-agent-select');
  const agentSlug = select ? select.value : '';

  const agentAttr = agentSlug ? ` data-agent="${agentSlug}"` : '';

  const popupCode = `<!-- S-Bot Support Agentic AI - Standalone Widget -->\n<script src="${origin}/widget/dist/sbot-widget.js"${agentAttr}></script>`;
  const iframeCode = `<!-- S-Bot Fullscreen Iframe -->\n<iframe src="${origin}/widget/fullscreen.html?tenant=sleman-default&lang=id${agentSlug ? '&agent=' + agentSlug : ''}" width="100%" height="650px" frameborder="0"></iframe>`;

  const taPopup = document.getElementById('snippet-popup');
  const taIframe = document.getElementById('snippet-iframe');
  if (taPopup) taPopup.value = popupCode;
  if (taIframe) taIframe.value = iframeCode;
}

function copySnippet(elementId) {
  const ta = document.getElementById(elementId);
  if (!ta) return;
  ta.select();
  navigator.clipboard.writeText(ta.value);
  alert('Kode snippet berhasil disalin ke clipboard!');
}
