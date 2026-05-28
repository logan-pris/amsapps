<?php 
$pageTitle = "AMS Apps - CP2 Fuel";
require_once __DIR__ . '/core/auth_check.php';
require_once __DIR__ . '/core/db.php'; 
include 'includes/header.php'; 
?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
      background: #f0f2f5;
      color: #1a1a2e;
      min-height: 100vh;
    }

    /* ── Header ── */
    header {
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  padding: 14px 36px;
  display: flex;
  align-items: center;
  /* This pushes the logo to the far left and the text to the far right */
  justify-content: space-between; 
}

.logocard-logo {
  display: flex;
  align-items: center;
}

.logocard-right {
  font-size: 0.72rem;
  color: #666;
  letter-spacing: 0.4px;
  white-space: nowrap; /* Prevents the text from wrapping to a new line */
}
    .logo-svg { height: 52px; width: auto; }
    

    .container {
      max-width: 860px;
      margin: 0 auto;
      padding: 32px 20px 24px;
    }

    /* ── Price Card ── */
    .cp2-card {
  background: white;
  border-radius: 18px;
  padding: 44px 40px 36px;
  text-align: center;
  box-shadow: 0 2px 24px rgba(0,0,0,0.07);
  margin-bottom: 20px;

  /* --- Add these lines --- */
  display: flex;
  align-items: center;       /* Vertically centers the logo and text */
  justify-content: space-between; /* Pushes logo to the left and text to the right */
}
    .price-card {
      background: white;
      border-radius: 18px;
      padding: 44px 40px 36px;
      text-align: center;
      box-shadow: 0 2px 24px rgba(0,0,0,0.07);
      margin-bottom: 20px;
    }
    .price-label {
      font-size: 0.78rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      color: #9ca3af;
      margin-bottom: 14px;
    }
    .price-main {
      font-size: 5rem;
      font-weight: 800;
      line-height: 1;
      color: #1a1a2e;
      margin-bottom: 18px;
      letter-spacing: -2px;
    }
    .price-main .unit {
      font-size: 1.6rem;
      font-weight: 400;
      color: #9ca3af;
      letter-spacing: 0;
    }
    .price-change {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 18px;
      border-radius: 999px;
      font-size: 0.88rem;
      font-weight: 600;
      margin-bottom: 14px;
    }
    .price-change.up   { background: #fee2e2; color: #dc2626; }
    .price-change.down { background: #dcfce7; color: #15803d; }
    .price-change.flat { background: #f3f4f6; color: #6b7280; }
    .price-date {
      font-size: 0.78rem;
      color: #b0b0b0;
    }

    /* ── Surcharge Card ── */
    .surcharge-card {
      background: #000;
      border-radius: 18px;
      padding: 28px 36px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      box-shadow: 0 2px 24px rgba(0,0,0,0.15);
    }
    .surcharge-left .sc-label {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      color: #666;
      margin-bottom: 8px;
    }
    .surcharge-left .sc-rate {
      font-size: 3.2rem;
      font-weight: 800;
      color: #FFC107;
      letter-spacing: -1px;
      line-height: 1;
    }
    .surcharge-left .sc-rate .sc-unit {
      font-size: 1.2rem;
      font-weight: 400;
      color: #a0870a;
    }
    .surcharge-left .sc-note {
      font-size: 0.75rem;
      color: #555;
      margin-top: 6px;
    }
    .surcharge-right {
      text-align: right;
    }
    .surcharge-right .sc-band-label {
      font-size: 0.72rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #555;
      margin-bottom: 4px;
    }
    .surcharge-right .sc-band {
      font-size: 1.1rem;
      font-weight: 700;
      color: #888;
    }

    /* ── Stats Row ── */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-bottom: 20px;
    }
    .stat-card {
      background: white;
      border-radius: 14px;
      padding: 20px 22px;
      box-shadow: 0 2px 24px rgba(0,0,0,0.07);
    }
    .stat-label {
      font-size: 0.73rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #9ca3af;
      margin-bottom: 6px;
    }
    .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1a1a2e;
      letter-spacing: -0.5px;
    }

    /* ── Chart Card ── */
    .chart-card {
      background: white;
      border-radius: 18px;
      padding: 28px 32px 24px;
      box-shadow: 0 2px 24px rgba(0,0,0,0.07);
      margin-bottom: 20px;
    }
    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      margin-bottom: 22px;
    }
    .chart-title {
      font-size: 0.78rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #9ca3af;
    }
    .chart-range { font-size: 0.75rem; color: #c0c0c0; }
    .chart-wrap { position: relative; height: 260px; }

    /* ── Notification Bar ── */
    .notif-bar {
      background: white;
      border-radius: 14px;
      padding: 16px 22px;
      box-shadow: 0 2px 24px rgba(0,0,0,0.07);
      display: flex;
      align-items: center;
      gap: 16px;
      justify-content: space-between;
    }
    .notif-text { flex: 1; font-size: 0.83rem; color: #666; line-height: 1.4; }
    .notif-btn {
      background: #1a1a2e;
      color: white;
      border: none;
      padding: 9px 20px;
      border-radius: 9px;
      font-size: 0.81rem;
      font-weight: 600;
      cursor: pointer;
      white-space: nowrap;
      transition: opacity 0.15s;
      flex-shrink: 0;
    }
    .notif-btn:hover:not(:disabled) { opacity: 0.82; }
    .notif-btn:disabled { background: #d1d5db; cursor: default; }
    .notif-btn.enabled { background: #15803d; }

    /* ── Loading / Error ── */
    .loading-state {
      display: flex; flex-direction: column; align-items: center;
      padding: 60px 20px; color: #bbb; gap: 14px;
    }
    .spinner {
      width: 32px; height: 32px;
      border: 3px solid #e5e7eb;
      border-top-color: #1a1a2e;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .error-state { text-align: center; padding: 48px 20px; color: #dc2626; font-size: 0.88rem; }

    footer {
      text-align: center;
      padding: 20px;
      font-size: 0.72rem;
      color: #c0c0c0;
    }
    footer a { color: #9ca3af; text-decoration: none; }
    footer a:hover { text-decoration: underline; }

    @media (max-width: 600px) {
      .price-main { font-size: 3.5rem; }
      .stats-row { grid-template-columns: 1fr; }
      header { padding: 12px 18px; }
      .surcharge-card { flex-direction: column; text-align: center; }
      .surcharge-right { text-align: center; }
    }
  </style>



<div class="container">

  <div id="cp2-card" class="cp2-card">
    <div class="logocard-logo">
    <!-- C2 Logo recreated as inline SVG -->
        <img class="logo-svg" src="data:image/png;base64,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" alt="CP2 Logistics" />
        
    </div>
    <div class="logocard-right">
            U.S. National Average &middot; EIA Weekly Data
        </div>
    

  </div>
  <!-- Current Price -->
  <div id="price-card" class="price-card">
    <div class="loading-state">
      <div class="spinner"></div>
      <span>Fetching latest prices…</span>
    </div>
  </div>

  <!-- Fuel Surcharge -->
  <div class="surcharge-card" id="surcharge-card" style="display:none">
    <div class="surcharge-left">
      <div class="sc-label">Current Fuel Surcharge</div>
      <div class="sc-rate" id="sc-rate">—<span class="sc-unit"> /mi</span></div>
      <div class="sc-note" id="sc-note"></div>
    </div>
    <div class="surcharge-right">
      <div class="sc-band-label">Price Band</div>
      <div class="sc-band" id="sc-band">—</div>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-row" id="stats-row" style="display:none">
    <div class="stat-card">
      <div class="stat-label">52-Week High</div>
      <div class="stat-value" id="stat-high">—</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">52-Week Low</div>
      <div class="stat-value" id="stat-low">—</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">52-Week Avg</div>
      <div class="stat-value" id="stat-avg">—</div>
    </div>
  </div>

  <!-- Chart -->
  <div class="chart-card">
    <div class="chart-header">
      <span class="chart-title">52-Week Price History</span>
      <span class="chart-range" id="chart-range">—</span>
    </div>
    <div class="chart-wrap">
      <canvas id="priceChart"></canvas>
    </div>
  </div>

  <!-- Notifications -->
  <div class="notif-bar">
    <p class="notif-text">Open this page any Tuesday to get a browser alert with the latest price and surcharge rate.</p>
    <button class="notif-btn" id="notifBtn" onclick="requestNotification()">Enable Alerts</button>
  </div>

</div>

<footer>
  Data from the <a href="https://www.eia.gov/petroleum/gasdiesel/" target="_blank">U.S. Energy Information Administration</a> &middot; Updated every Tuesday
</footer>

<script>
  const API_KEY = 'fEHQ6dpfd8Ti6ipbvdJj969fWAqmOPqiRfjEcF7P';
  const EIA_URL = [
    'https://api.eia.gov/v2/petroleum/pri/gnd/data/',
    '?api_key=', API_KEY,
    '&frequency=weekly&data[0]=value',
    '&facets[product][]=EPD2D&facets[duoarea][]=NUS',
    '&sort[0][column]=period&sort[0][direction]=desc&length=52'
  ].join('');

  // ── Fuel Surcharge Matrix (from Fuel Surcharge Matrix.numbers) ──────────
  // [startPrice, endPrice, surchargePerMile]
  const SURCHARGE_MATRIX = [
    [3.20, 3.29, 0.00],
    [3.30, 3.39, 0.02],
    [3.40, 3.49, 0.03],
    [3.50, 3.59, 0.06],
    [3.60, 3.69, 0.08],
    [3.70, 3.79, 0.09],
    [3.80, 3.89, 0.11],
    [3.90, 3.99, 0.12],
    [4.00, 4.09, 0.14],
    [4.10, 4.19, 0.15],
    [4.20, 4.29, 0.17],
    [4.30, 4.39, 0.18],
    [4.40, 4.49, 0.20],
    [4.50, 4.59, 0.21],
    [4.60, 4.69, 0.24],
    [4.70, 4.79, 0.26],
    [4.80, 4.89, 0.27],
    [4.90, 4.99, 0.29],
    [5.00, 5.09, 0.30],
    [5.10, 5.19, 0.32],
    [5.20, 5.29, 0.34],
    [5.30, 5.39, 0.35],
    [5.40, 5.49, 0.37],
    [5.50, 5.59, 0.39],
    [5.60, 5.69, 0.40],
    [5.70, 5.79, 0.42],
    [5.80, 5.89, 0.44],
    [5.90, 5.99, 0.45],
  ];

  function lookupSurcharge(price) {
    for (const [start, end, rate] of SURCHARGE_MATRIX) {
      if (price >= start && price <= end + 0.009) {
        return { rate, band: `$${start.toFixed(2)} – $${end.toFixed(2)}` };
      }
    }
    if (price < SURCHARGE_MATRIX[0][0]) {
      return { rate: 0.00, band: `Below $${SURCHARGE_MATRIX[0][0].toFixed(2)}` };
    }
    return null; // above matrix range
  }

  let chartInstance = null;

  async function fetchData() {
    const res = await fetch(EIA_URL);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const json = await res.json();
    return json.response.data;
  }

  function fmt(val) { return '$' + parseFloat(val).toFixed(3); }

  function renderPriceCard(cur, prev) {
    const c = parseFloat(cur.value);
    const p = parseFloat(prev.value);
    const diff = c - p;
    const pct  = ((diff / p) * 100).toFixed(1);
    const cls  = diff >  0.001 ? 'up' : diff < -0.001 ? 'down' : 'flat';
    const arrow= diff >  0.001 ? '▲' : diff < -0.001 ? '▼' : '—';
    const sign = diff >  0.001 ? '+' : '';

    document.getElementById('price-card').innerHTML = `
      <div class="price-label">U.S. National Average Diesel</div>
      <div class="price-main">${fmt(c)}<span class="unit"> /gal</span></div>
      <div class="price-change ${cls}">
        ${arrow} ${sign}${diff.toFixed(3)} &nbsp;(${sign}${pct}%) vs last week
      </div>
      <div class="price-date">Week of ${cur.period}</div>
    `;
  }

  function renderSurcharge(price) {
    const result = lookupSurcharge(price);
    const card = document.getElementById('surcharge-card');
    card.style.display = 'flex';

    if (!result) {
      document.getElementById('sc-rate').innerHTML = 'N/A';
      document.getElementById('sc-note').textContent = 'Price above matrix range — check your surcharge schedule.';
      document.getElementById('sc-band').textContent = `> $${SURCHARGE_MATRIX[SURCHARGE_MATRIX.length-1][1].toFixed(2)}`;
      return;
    }

    document.getElementById('sc-rate').innerHTML =
      `$${result.rate.toFixed(2)}<span class="sc-unit"> /mi</span>`;
    document.getElementById('sc-note').textContent =
      result.rate === 0.00 ? 'No surcharge at this price level.' : 'Based on your fuel surcharge matrix.';
    document.getElementById('sc-band').textContent = result.band;
  }

  function renderStats(data) {
    const vals = data.map(d => parseFloat(d.value));
    document.getElementById('stat-high').textContent = fmt(Math.max(...vals));
    document.getElementById('stat-low').textContent  = fmt(Math.min(...vals));
    document.getElementById('stat-avg').textContent  = fmt(vals.reduce((a,b)=>a+b,0)/vals.length);
    document.getElementById('stats-row').style.display = 'grid';
  }

  function renderChart(data) {
    const sorted = [...data].reverse();
    const labels = sorted.map(d => d.period);
    const values = sorted.map(d => parseFloat(d.value));

    document.getElementById('chart-range').textContent =
      `${labels[0]} → ${labels[labels.length-1]}`;

    const ctx = document.getElementById('priceChart').getContext('2d');
    if (chartInstance) chartInstance.destroy();

    const grad = ctx.createLinearGradient(0, 0, 0, 260);
    grad.addColorStop(0, 'rgba(26,26,46,0.12)');
    grad.addColorStop(1, 'rgba(26,26,46,0)');

    chartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          data: values,
          borderColor: '#1a1a2e',
          backgroundColor: grad,
          borderWidth: 2.5,
          pointRadius: 0,
          pointHoverRadius: 5,
          pointHoverBackgroundColor: '#1a1a2e',
          fill: true,
          tension: 0.35,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1a1a2e',
            titleFont: { size: 11 },
            bodyFont: { size: 13, weight: '700' },
            padding: 10,
            callbacks: {
              label: ctx => `  ${fmt(ctx.parsed.y)} / gal`
            }
          }
        },
        scales: {
          x: {
            ticks: { maxTicksLimit: 9, font: { size: 10 }, color: '#b0b0b0', maxRotation: 0 },
            grid: { display: false },
            border: { display: false },
          },
          y: {
            ticks: { callback: v => `$${v.toFixed(2)}`, font: { size: 10 }, color: '#b0b0b0' },
            grid: { color: '#f3f4f6' },
            border: { display: false },
          }
        }
      }
    });
  }

  async function init() {
    try {
      const data = await fetchData();
      const currentPrice = parseFloat(data[0].value);
      renderPriceCard(data[0], data[1]);
      renderSurcharge(currentPrice);
      renderStats(data);
      renderChart(data);
      updateNotifButton();
      checkTuesdayAlert(data[0]);
    } catch (err) {
      document.getElementById('price-card').innerHTML =
        `<div class="error-state">⚠ Couldn't load price data.<br><small>${err.message}</small></div>`;
      console.error(err);
    }
  }

  // ── Notifications ──────────────────────────────────────────────────────
  function updateNotifButton() {
    const btn = document.getElementById('notifBtn');
    if (!('Notification' in window)) {
      btn.disabled = true; btn.textContent = 'Not supported'; return;
    }
    if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
      btn.textContent = 'Alerts On ✓'; btn.disabled = true; btn.classList.add('enabled');
    } else if (typeof Notification !== 'undefined' && Notification.permission === 'denied') {
      btn.textContent = 'Blocked in browser'; btn.disabled = true;
    }
  }

  async function requestNotification() {
    if (!('Notification' in window)) return;
    const perm = await Notification.requestPermission();
    updateNotifButton();
    if (perm === 'granted') {
      new Notification('Diesel Price Tracker', {
        body: "You're all set — Tuesday alerts enabled."
      });
    }
  }

  function checkTuesdayAlert(current) {
    if (typeof Notification === 'undefined' || Notification.permission !== 'granted') return;
    const today = new Date();
    if (today.getDay() !== 2) return;
    const key = 'diesel-alerted-' + today.toISOString().slice(0, 10);
    if (localStorage.getItem(key)) return;
    const price  = fmt(current.value);
    const result = lookupSurcharge(parseFloat(current.value));
    const scText = result ? ` · Surcharge: $${result.rate.toFixed(2)}/mi` : '';
    new Notification('⛽ New Diesel Price — ' + price + '/gal', {
      body: `Week of ${current.period}${scText}`,
      tag: 'diesel-tuesday'
    });
    localStorage.setItem(key, '1');
  }

  init();
</script>

</body>
</html>
