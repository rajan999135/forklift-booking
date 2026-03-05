@extends('layouts.admin')

@section('admin-content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

<style>
/* ══════════════════════════════════════════════════════
   FULL BLEED ESCAPE — override admin layout completely
══════════════════════════════════════════════════════ */
#app, main, .main-content, .content-wrapper,
.container, .container-fluid, [class*="content"],
[class*="wrapper"], [class*="main"] {
  padding: 0 !important;
  margin: 0 !important;
  max-width: none !important;
  background: #0b0f1a !important;
}

body {
  background: #0b0f1a !important;
  overflow-x: hidden;
}

.rp-escape {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: #0b0f1a;
  z-index: -1;
}

/* Remove any inherited box constraints */
.rp-shell {
  position: relative;
  width: 100vw;
  margin-left: calc(-50vw + 50%);
  background: #0b0f1a;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', sans-serif;
  color: #e8edf8;
}

/* ══════════════════════════════════════════════════════
   DESIGN TOKENS
══════════════════════════════════════════════════════ */
:root {
  --bg:      #0b0f1a;
  --s1:      #111827;
  --s2:      #162033;
  --s3:      #1e2d42;
  --border:  rgba(255,255,255,0.07);
  --border2: rgba(255,255,255,0.13);

  --text:  #e8edf8;
  --text2: #8b9bb8;
  --text3: #4e5e7a;

  --gold:   #f5c542;
  --teal:   #2dd4bf;
  --sky:    #60a5fa;
  --rose:   #f87171;
  --violet: #a78bfa;
  --amber:  #fbbf24;
  --green:  #34d399;
  --orange: #fb923c;

  --r: 12px;
  --r-sm: 8px;

  --mono: 'JetBrains Mono', monospace;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ══════════════════════════════════════════════════════
   STICKY TOP BAR
══════════════════════════════════════════════════════ */
.topbar {
  position: sticky;
  top: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 40px;
  background: rgba(11,15,26,0.96);
  backdrop-filter: blur(24px);
  border-bottom: 1px solid var(--border);
}

.tb-title { font-size: 1.1rem; font-weight: 800; color: var(--text); letter-spacing: -0.3px; }
.tb-sub   { font-size: 0.68rem; color: var(--text3); margin-top: 2px; }

.tb-right { display: flex; align-items: center; gap: 10px; }

.live-chip {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 13px;
  background: rgba(45,212,191,0.08);
  border: 1px solid rgba(45,212,191,0.2);
  border-radius: 99px;
  font-size: 0.68rem; font-weight: 700; color: var(--teal);
}
.live-dot {
  width: 6px; height: 6px; border-radius: 50%; background: var(--teal);
  animation: pulse2 2s ease-in-out infinite;
}
@keyframes pulse2 {
  0%,100% { box-shadow: 0 0 0 0 rgba(45,212,191,0.5); }
  50%      { box-shadow: 0 0 0 5px rgba(45,212,191,0); }
}

.btn-csv {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 8px 18px;
  background: var(--s2); border: 1px solid var(--border2);
  border-radius: var(--r-sm); color: var(--text);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.77rem; font-weight: 700; text-decoration: none;
  transition: all 0.15s;
}
.btn-csv:hover { background: var(--s3); border-color: rgba(255,255,255,0.22); transform: translateY(-1px); }

/* ══════════════════════════════════════════════════════
   MAIN GRID BODY
══════════════════════════════════════════════════════ */
.rp-body {
  padding: 32px 40px 60px;
  display: flex;
  flex-direction: column;
  gap: 32px;
}

/* ── Section label ── */
.sec-label {
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  color: var(--text3);
  margin-bottom: -18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.sec-link { font-size: 0.72rem; font-weight: 600; color: var(--sky); text-decoration: none; letter-spacing: 0; text-transform: none; }
.sec-link:hover { color: #93c5fd; }

/* ── Cards ── */
.card {
  background: var(--s1);
  border: 1px solid var(--border);
  border-radius: var(--r);
  padding: 24px 26px;
}

.card-hd { margin-bottom: 18px; }
.card-title { font-size: 0.88rem; font-weight: 700; color: var(--text); }
.card-sub   { font-size: 0.67rem; color: var(--text3); margin-top: 3px; }

/* ══════════════════════════════════════════════════════
   ROW 1 — KPI CARDS  (4-col)
══════════════════════════════════════════════════════ */
.kpi-grid {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr 1fr;
  gap: 16px;
}

.kpi {
  background: var(--s1);
  border: 1px solid var(--border);
  border-radius: var(--r);
  padding: 24px 24px 22px;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}
.kpi:hover { transform: translateY(-2px); border-color: var(--border2); }

/* top stripe */
.kpi::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 3px;
  border-radius: var(--r) var(--r) 0 0;
}
.kpi.gold::before   { background: linear-gradient(90deg,#c9a217,var(--gold),#ffe57a); }
.kpi.sky::before    { background: linear-gradient(90deg,#1d4ed8,var(--sky),#93c5fd); }
.kpi.rose::before   { background: linear-gradient(90deg,#be123c,var(--rose),#fca5a5); }
.kpi.violet::before { background: linear-gradient(90deg,#5b21b6,var(--violet),#ddd6fe); }

.kpi-top {
  display: flex; align-items: flex-start; justify-content: space-between;
  margin-bottom: 16px;
}

.kpi-icon {
  width: 40px; height: 40px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.15rem; flex-shrink: 0;
}
.kpi-icon.gold   { background: rgba(245,197,66,0.1);  border: 1px solid rgba(245,197,66,0.16); }
.kpi-icon.sky    { background: rgba(96,165,250,0.1);  border: 1px solid rgba(96,165,250,0.16); }
.kpi-icon.rose   { background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.16); }
.kpi-icon.violet { background: rgba(167,139,250,0.1); border: 1px solid rgba(167,139,250,0.16); }

.kpi-label {
  font-size: 0.6rem; font-weight: 700; letter-spacing: 2px;
  text-transform: uppercase; color: var(--text3); margin-bottom: 7px;
}

.kpi-val {
  font-size: 2.3rem; font-weight: 800; line-height: 1; letter-spacing: -2px;
  font-family: var(--mono);
}
.kpi-val.gold   { color: var(--gold); }
.kpi-val.sky    { color: var(--sky); }
.kpi-val.rose   { color: var(--rose); }
.kpi-val.violet { color: var(--violet); }

.kpi-desc { font-size: 0.69rem; color: var(--text2); margin-top: 6px; }
.kpi-desc strong { color: var(--text); font-weight: 700; }

/* pay split */
.pay-split { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 4px; }

.pay-blk {
  background: var(--s2); border: 1px solid var(--border);
  border-radius: var(--r-sm); padding: 12px 14px;
}
.pay-lbl { font-size: 0.58rem; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; margin-bottom: 5px; }
.pay-lbl.sky  { color: var(--sky); }
.pay-lbl.teal { color: var(--teal); }
.pay-amt { font-family: var(--mono); font-size: 1rem; font-weight: 600; }
.pay-amt.sky  { color: var(--sky); }
.pay-amt.teal { color: var(--teal); }
.pay-cnt { font-size: 0.62rem; color: var(--text3); margin-top: 3px; }
.pay-bar { height: 3px; background: rgba(255,255,255,0.06); border-radius: 3px; margin-top: 9px; overflow: hidden; }
.pay-fill { height: 100%; border-radius: 3px; }

/* pill row */
.pill-row { display: flex; gap: 8px; margin-top: 6px; }
.pill {
  flex: 1; background: var(--s2); border: 1px solid var(--border);
  border-radius: var(--r-sm); padding: 10px 8px; text-align: center;
}
.pill-l { font-size: 0.56rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text3); display: block; margin-bottom: 4px; }
.pill-v { font-family: var(--mono); font-size: 1.15rem; font-weight: 600; display: block; }

.prog { height: 5px; background: var(--s2); border-radius: 5px; overflow: hidden; margin-top: 16px; }
.prog-f { height: 100%; border-radius: 5px; background: linear-gradient(90deg,#5b21b6,var(--violet)); transition: width 1.2s ease; }

/* ══════════════════════════════════════════════════════
   ROW 2 — CHARTS (3-col)
══════════════════════════════════════════════════════ */
.r2 { display: grid; grid-template-columns: 1fr 0.75fr 1.6fr; gap: 16px; }

/* Bar chart */
.bar-chart {
  display: flex; align-items: flex-end; gap: 10px;
  height: 160px; padding-bottom: 2px;
}
.bc { flex: 1; height: 100%; display: flex; flex-direction: column; align-items: center; gap: 5px; }
.bc-n {
  font-family: var(--mono); font-size: 0.65rem; font-weight: 600;
}
.bc-track { flex: 1; width: 100%; display: flex; align-items: flex-end; }
.bc-bar {
  width: 100%; border-radius: 6px 6px 0 0; min-height: 4px;
  position: relative; cursor: pointer; transition: filter 0.15s;
}
.bc-bar:hover { filter: brightness(1.2); }
.bc-bar.cur { background: linear-gradient(to top, #0d9488, var(--teal)); }
.bc-bar.old { background: var(--s3); border: 1px solid var(--border); }
.bc-bar:hover::after {
  content: attr(data-tip);
  position: absolute; bottom: calc(100%+6px); left: 50%; transform: translateX(-50%);
  background: var(--s3); border: 1px solid var(--border2);
  color: var(--text); font-size: 0.63rem; font-family: var(--mono);
  padding: 4px 9px; border-radius: 6px; white-space: nowrap; z-index: 20;
  pointer-events: none;
}
.bc-l { font-size: 0.62rem; color: var(--text3); font-weight: 600; }

/* Status list */
.st-list { display: flex; flex-direction: column; gap: 14px; }
.st-row  { display: flex; flex-direction: column; gap: 6px; }
.st-top  { display: flex; justify-content: space-between; align-items: center; }
.st-name { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 600; color: var(--text); }
.st-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.st-cnt  { font-family: var(--mono); font-size: 0.7rem; font-weight: 600; color: var(--text2); }
.st-bar  { height: 6px; background: var(--s2); border-radius: 4px; overflow: hidden; }
.st-fill { height: 100%; border-radius: 4px; transition: width 1s ease; }

/* Equipment table */
.eq-tbl  { width: 100%; border-collapse: collapse; }
.eq-tbl th {
  padding: 0 14px 12px; text-align: left;
  font-size: 0.58rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
  color: var(--text3); border-bottom: 1px solid var(--border); white-space: nowrap;
}
.eq-tbl td {
  padding: 13px 14px; font-size: 0.8rem; color: var(--text);
  border-bottom: 1px solid rgba(255,255,255,0.03);
}
.eq-tbl tr:last-child td { border-bottom: none; }
.eq-tbl tr:hover td { background: rgba(255,255,255,0.015); }
.eq-nm-cell { display: flex; align-items: center; gap: 10px; }
.eq-ico {
  width: 32px; height: 32px;
  background: linear-gradient(135deg,rgba(245,197,66,0.15),rgba(245,197,66,0.06));
  border: 1px solid rgba(245,197,66,0.18); border-radius: 9px;
  display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;
}
.eq-nm { font-weight: 700; font-size: 0.8rem; }
.ut-wrap { display: flex; align-items: center; gap: 10px; }
.ut-track { flex: 1; height: 5px; background: var(--s2); border-radius: 3px; overflow: hidden; }
.ut-fill { height: 100%; border-radius: 3px; background: linear-gradient(90deg,#5b21b6,var(--violet)); }
.ut-pct { font-family: var(--mono); font-size: 0.68rem; font-weight: 600; color: var(--violet); width: 36px; text-align: right; }

/* ══════════════════════════════════════════════════════
   ROW 3 — DEVICE / HEATMAP / FUNNEL  (3-col)
══════════════════════════════════════════════════════ */
.r3 { display: grid; grid-template-columns: 1fr 1.05fr 1.2fr; gap: 16px; }

.divider { height: 1px; background: var(--border); margin: 18px 0; }

.sub-hd {
  font-size: 0.62rem; font-weight: 700; letter-spacing: 2px;
  text-transform: uppercase; color: var(--text3); margin-bottom: 12px;
  display: flex; align-items: center; gap: 7px;
}

/* device rows */
.dev-list { display: flex; flex-direction: column; gap: 9px; }
.dev-row {
  display: flex; align-items: center; gap: 12px;
  padding: 11px 14px; background: var(--s2);
  border: 1px solid var(--border); border-radius: 10px;
  transition: border-color 0.15s;
}
.dev-row:hover { border-color: var(--border2); }
.dev-emoji { font-size: 1.05rem; width: 26px; flex-shrink: 0; text-align: center; }
.dev-info  { flex: 1; min-width: 0; }
.dev-name  { font-size: 0.78rem; font-weight: 700; color: var(--text); margin-bottom: 5px; }
.dev-track { height: 4px; border-radius: 3px; background: rgba(255,255,255,0.05); overflow: hidden; }
.dev-fill  { height: 100%; border-radius: 3px; }
.dev-pct   { font-family: var(--mono); font-size: 0.88rem; font-weight: 600; flex-shrink: 0; width: 40px; text-align: right; }

/* geo list */
.geo-list { display: flex; flex-direction: column; gap: 9px; }
.geo-row  { display: flex; align-items: center; gap: 9px; }
.geo-flag { font-size: 1rem; flex-shrink: 0; }
.geo-info { flex: 1; }
.geo-nm   { font-size: 0.75rem; font-weight: 600; color: var(--text); margin-bottom: 4px; }
.geo-bar  { height: 3px; background: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden; }
.geo-fill { height: 100%; border-radius: 3px; background: linear-gradient(90deg,#4338ca,var(--violet)); }
.geo-pct  { font-family: var(--mono); font-size: 0.7rem; font-weight: 600; color: var(--violet); flex-shrink: 0; width: 34px; text-align: right; }

/* heatmap */
.hm-wrap { display: grid; grid-template-columns: repeat(12,1fr); gap: 5px; }
.hm-cell {
  aspect-ratio: 1; border-radius: 5px; cursor: pointer;
  position: relative; transition: transform 0.1s;
}
.hm-cell:hover { transform: scale(1.3); z-index: 5; }
.hm-cell:hover::after {
  content: attr(data-tip);
  position: absolute; bottom: calc(100%+6px); left: 50%; transform: translateX(-50%);
  background: var(--s3); border: 1px solid var(--border2);
  color: var(--text); font-size: 0.62rem; font-family: var(--mono);
  padding: 4px 9px; border-radius: 6px; white-space: nowrap; z-index: 20;
  pointer-events: none;
}
.hm-lbl-row { display: grid; grid-template-columns: repeat(12,1fr); gap: 5px; margin-top: 5px; }
.hm-lbl     { font-size: 0.52rem; color: var(--text3); text-align: center; font-weight: 600; }
.legend     { display: flex; align-items: center; justify-content: space-between; margin-top: 9px; }
.legend-txt { font-size: 0.6rem; color: var(--text3); }
.legend-sw  { display: flex; gap: 3px; }
.sw         { width: 12px; height: 12px; border-radius: 3px; }

/* peak */
.peak-list { display: flex; flex-direction: column; gap: 10px; }
.pk-row    { display: flex; flex-direction: column; gap: 5px; }
.pk-top    { display: flex; justify-content: space-between; }
.pk-time   { font-size: 0.77rem; font-weight: 700; color: var(--text); }
.pk-pct    { font-family: var(--mono); font-size: 0.7rem; font-weight: 600; color: var(--violet); }
.pk-bar    { height: 5px; background: var(--s2); border-radius: 4px; overflow: hidden; }
.pk-fill   { height: 100%; border-radius: 4px; background: linear-gradient(90deg,#4338ca,var(--violet)); }

/* funnel */
.fn-list { display: flex; flex-direction: column; gap: 12px; }
.fn-row  { display: flex; flex-direction: column; gap: 6px; }
.fn-top  { display: flex; align-items: center; justify-content: space-between; }
.fn-lbl  { font-size: 0.8rem; font-weight: 600; color: var(--text); }
.fn-val  { font-family: var(--mono); font-size: 0.82rem; font-weight: 700; }
.fn-bar  { height: 8px; border-radius: 5px; background: var(--s2); overflow: hidden; }
.fn-fill { height: 100%; border-radius: 5px; transition: width 1s ease; }

/* page rows */
.pg-list { display: flex; flex-direction: column; gap: 7px; }
.pg-row  {
  display: flex; align-items: center; justify-content: space-between;
  padding: 9px 13px; background: var(--s2);
  border: 1px solid var(--border); border-radius: var(--r-sm);
  transition: border-color 0.15s;
}
.pg-row:hover { border-color: var(--border2); }
.pg-path  { font-family: var(--mono); font-size: 0.73rem; font-weight: 600; color: var(--sky); }
.pg-meta  { display: flex; align-items: center; gap: 12px; }
.pg-views { font-family: var(--mono); font-size: 0.73rem; font-weight: 700; color: var(--text); }
.pg-bounce{ font-size: 0.65rem; color: var(--text3); }

/* ══════════════════════════════════════════════════════
   ROW 4 — TRANSACTIONS
══════════════════════════════════════════════════════ */
.tx-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 10px; }
.tx-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 18px; background: var(--s1);
  border: 1px solid var(--border); border-radius: 12px;
  text-decoration: none; color: inherit;
  transition: all 0.15s;
}
.tx-row:hover { background: var(--s2); border-color: var(--border2); transform: translateY(-1px); box-shadow: 0 8px 28px rgba(0,0,0,0.22); }
.tx-left { display: flex; align-items: center; gap: 13px; min-width: 0; }
.tx-av {
  width: 38px; height: 38px; border-radius: 50%;
  background: linear-gradient(135deg,#1e3a5f,#1d4ed8);
  border: 1px solid rgba(96,165,250,0.22);
  display: flex; align-items: center; justify-content: center;
  font-size: 0.8rem; font-weight: 800; color: var(--sky); flex-shrink: 0;
}
.tx-info { min-width: 0; }
.tx-name { font-size: 0.82rem; font-weight: 700; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.tx-meta { font-size: 0.66rem; color: var(--text3); margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.tx-right{ display: flex; align-items: center; gap: 11px; flex-shrink: 0; }
.tx-amt  { font-family: var(--mono); font-size: 0.84rem; font-weight: 700; }
.tx-amt.card { color: var(--sky); }
.tx-amt.cash { color: var(--teal); }

.badge { display: inline-block; padding: 3px 11px; border-radius: 99px; font-size: 0.63rem; font-weight: 700; white-space: nowrap; }
.b-confirmed  { background: rgba(52,211,153,0.1);  color:#34d399; border:1px solid rgba(52,211,153,0.2); }
.b-awaiting   { background: rgba(96,165,250,0.1);  color:var(--sky); border:1px solid rgba(96,165,250,0.2); }
.b-pending    { background: rgba(251,191,36,0.1);  color:var(--amber); border:1px solid rgba(251,191,36,0.2); }
.b-completed  { background: rgba(167,139,250,0.1); color:var(--violet); border:1px solid rgba(167,139,250,0.2); }
.b-cancelled  { background: rgba(248,113,113,0.1); color:var(--rose); border:1px solid rgba(248,113,113,0.2); }
.b-rejected   { background: rgba(251,146,60,0.1);  color:var(--orange); border:1px solid rgba(251,146,60,0.2); }

/* ══════════════════════════════════════════════════════
   ANIMATIONS
══════════════════════════════════════════════════════ */
@keyframes fu { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.a1{animation:fu .4s ease both .04s}
.a2{animation:fu .4s ease both .12s}
.a3{animation:fu .4s ease both .20s}
.a4{animation:fu .4s ease both .28s}
.a5{animation:fu .4s ease both .36s}

/* ══════════════════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════════════════ */
@media(max-width:1400px) {
  .rp-body, .topbar { padding-left:28px; padding-right:28px; }
}
@media(max-width:1200px) {
  .kpi-grid { grid-template-columns: repeat(2,1fr); }
  .r2 { grid-template-columns: 1fr 0.8fr; }
  .r2 .card:last-child { grid-column: span 2; }
  .r3 { grid-template-columns: 1fr 1fr; }
  .r3 .card:last-child { grid-column: span 2; }
}
@media(max-width:768px) {
  .rp-body, .topbar { padding-left:16px; padding-right:16px; }
  .r2, .r3 { grid-template-columns: 1fr; }
  .r2 .card:last-child, .r3 .card:last-child { grid-column: span 1; }
  .tx-grid { grid-template-columns: 1fr; }
  .kpi-grid { grid-template-columns: 1fr 1fr; }
}
</style>

{{-- Full-page background fix --}}
<div class="rp-escape"></div>

<div class="rp-shell">

  {{-- ── TOP BAR ── --}}
  <div class="topbar">
    <div>
      <div class="tb-title">Analytics &amp; Reports</div>
      <div class="tb-sub">Forklift Booking · Live Operations Dashboard</div>
    </div>
    <div class="tb-right">
      <div class="live-chip"><div class="live-dot"></div>Live</div>
      <a href="{{ route('admin.reports.export') }}" class="btn-csv">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export CSV
      </a>
    </div>
  </div>

  <div class="rp-body">

    @if(session('success'))
    <div style="padding:11px 16px;background:rgba(52,211,153,0.07);border:1px solid rgba(52,211,153,0.18);border-radius:9px;color:#34d399;font-size:0.77rem;font-weight:600;display:flex;align-items:center;gap:9px;">
      <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      {{ session('success') }}
    </div>
    @endif

    @php
      $cardRev  = $stats['card_revenue']  ?? 0;
      $cashRev  = $stats['cash_revenue']  ?? 0;
      $totalRev = $cardRev + $cashRev;
      $cardCnt  = $stats['card_bookings_count'] ?? 0;
      $cashCnt  = $stats['cash_bookings_count'] ?? 0;
      $tot2     = max($cardCnt + $cashCnt, 1);
      $cPct     = round($cardCnt / $tot2 * 100);
      $xPct     = 100 - $cPct;
    @endphp

    {{-- ══ ROW 1: KPI ══ --}}
    <div class="sec-label a1">KEY METRICS</div>

    <div class="kpi-grid a1">

      {{-- Revenue --}}
      <div class="kpi gold">
        <div class="kpi-top">
          <div>
            <div class="kpi-label">Total Revenue</div>
            <div class="kpi-val gold">C${{ number_format($totalRev/100, 2) }}</div>
            <div class="kpi-desc">This month: <strong>C${{ number_format(($stats['monthly_revenue']??0)/100,2) }}</strong></div>
          </div>
          <div class="kpi-icon gold">💰</div>
        </div>
        <div class="pay-split">
          <div class="pay-blk">
            <div class="pay-lbl sky">💳 Card</div>
            <div class="pay-amt sky">C${{ number_format($cardRev/100,2) }}</div>
            <div class="pay-cnt">{{ $cardCnt }} transactions</div>
            <div class="pay-bar"><div class="pay-fill" style="width:{{ $cPct }}%;background:linear-gradient(90deg,#1d4ed8,var(--sky))"></div></div>
          </div>
          <div class="pay-blk">
            <div class="pay-lbl teal">💵 Cash</div>
            <div class="pay-amt teal">C${{ number_format($cashRev/100,2) }}</div>
            <div class="pay-cnt">{{ $cashCnt }} transactions</div>
            <div class="pay-bar"><div class="pay-fill" style="width:{{ $xPct }}%;background:linear-gradient(90deg,#0d9488,var(--teal))"></div></div>
          </div>
        </div>
      </div>

      {{-- Bookings --}}
      <div class="kpi sky">
        <div class="kpi-top">
          <div>
            <div class="kpi-label">Total Bookings</div>
            <div class="kpi-val sky">{{ $stats['total_bookings']??0 }}</div>
            <div class="kpi-desc">All time · all equipment</div>
          </div>
          <div class="kpi-icon sky">📋</div>
        </div>
        <div class="pill-row">
          <div class="pill"><span class="pill-l">Pending</span><span class="pill-v" style="color:var(--amber)">{{ $stats['pending_bookings']??0 }}</span></div>
          <div class="pill"><span class="pill-l">Active</span><span class="pill-v" style="color:var(--teal)">{{ $stats['confirmed']??0 }}</span></div>
          <div class="pill"><span class="pill-l">Done</span><span class="pill-v" style="color:var(--violet)">{{ $stats['completed']??0 }}</span></div>
        </div>
      </div>

      {{-- Refunds --}}
      <div class="kpi rose">
        <div class="kpi-top">
          <div>
            <div class="kpi-label">Total Refunds</div>
            <div class="kpi-val rose">C${{ number_format(($stats['total_refunds']??0)/100,2) }}</div>
            <div class="kpi-desc">Card refunds processed</div>
          </div>
          <div class="kpi-icon rose">↩️</div>
        </div>
        <div class="pill-row">
          <div class="pill"><span class="pill-l">Cancelled</span><span class="pill-v" style="color:var(--rose)">{{ $stats['cancelled']??0 }}</span></div>
          <div class="pill"><span class="pill-l">Rejected</span><span class="pill-v" style="color:var(--orange)">{{ $stats['rejected']??0 }}</span></div>
        </div>
      </div>

      {{-- Completion --}}
      <div class="kpi violet">
        <div class="kpi-top">
          <div>
            <div class="kpi-label">Completion Rate</div>
            <div class="kpi-val violet">{{ $stats['completion_rate']??0 }}%</div>
            <div class="kpi-desc">{{ $stats['completed']??0 }} jobs completed</div>
          </div>
          <div class="kpi-icon violet">✅</div>
        </div>
        <div class="prog"><div class="prog-f" style="width:{{ min($stats['completion_rate']??0,100) }}%"></div></div>
      </div>

    </div>{{-- /kpi-grid --}}

    {{-- ══ ROW 2: CHARTS ══ --}}
    <div class="sec-label a2">PERFORMANCE BREAKDOWN</div>

    <div class="r2 a2">

      {{-- Monthly bar chart --}}
      <div class="card">
        <div class="card-hd">
          <div class="card-title">Monthly Bookings</div>
          <div class="card-sub">Last 6 months — current month highlighted in teal</div>
        </div>
        @php $md=$stats['monthly_bookings']??[]; $mx=max(array_column($md,'count')?:[1]); @endphp
        <div class="bar-chart">
          @foreach($md as $m)
            @php $h=$mx>0?round(($m['count']/$mx)*100):0; $cur=$m['label']===now()->format('M'); @endphp
            <div class="bc">
              <span class="bc-n" style="color:{{ $cur?'var(--teal)':'var(--text3)' }}">{{ $m['count'] }}</span>
              <div class="bc-track">
                <div class="bc-bar {{ $cur?'cur':'old' }}" style="height:{{ max($h,3) }}%" data-tip="{{ $m['count'] }} bookings · {{ $m['label'] }}"></div>
              </div>
              <span class="bc-l" style="color:{{ $cur?'var(--teal)':'var(--text3)' }}">{{ $m['label'] }}</span>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Status --}}
      <div class="card">
        <div class="card-hd">
          <div class="card-title">Booking Status</div>
          <div class="card-sub">All-time distribution</div>
        </div>
        @php
          $tb=max($stats['total_bookings']??1,1);
          $ss=[
            ['l'=>'Confirmed','c'=>$stats['confirmed']??0,'dot'=>'#34d399','fill'=>'linear-gradient(90deg,#065f46,#34d399)'],
            ['l'=>'Completed','c'=>$stats['completed']??0,'dot'=>'#a78bfa','fill'=>'linear-gradient(90deg,#4c1d95,#a78bfa)'],
            ['l'=>'Pending',  'c'=>$stats['pending_bookings']??0,'dot'=>'#fbbf24','fill'=>'linear-gradient(90deg,#78350f,#fbbf24)'],
            ['l'=>'Cancelled','c'=>$stats['cancelled']??0,'dot'=>'#f87171','fill'=>'linear-gradient(90deg,#7f1d1d,#f87171)'],
            ['l'=>'Rejected', 'c'=>$stats['rejected']??0,'dot'=>'#fb923c','fill'=>'linear-gradient(90deg,#7c2d12,#fb923c)'],
          ];
        @endphp
        <div class="st-list">
          @foreach($ss as $s)
            @php $p=round($s['c']/$tb*100); @endphp
            <div class="st-row">
              <div class="st-top">
                <span class="st-name"><span class="st-dot" style="background:{{ $s['dot'] }}"></span>{{ $s['l'] }}</span>
                <span class="st-cnt">{{ $s['c'] }} &nbsp;·&nbsp; {{ $p }}%</span>
              </div>
              <div class="st-bar"><div class="st-fill" style="width:{{ $p }}%;background:{{ $s['fill'] }}"></div></div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Equipment --}}
      <div class="card">
        <div class="card-hd">
          <div class="card-title">Equipment Performance</div>
          <div class="card-sub">Revenue &amp; utilization per forklift unit</div>
        </div>
        @if(count($equipmentStats)>0)
        <table class="eq-tbl">
          <thead><tr><th>Equipment</th><th>Jobs</th><th>💳 Card</th><th>💵 Cash</th><th>Total</th><th>Utilization</th></tr></thead>
          <tbody>
            @foreach($equipmentStats as $eq)
            <tr>
              <td><div class="eq-nm-cell"><div class="eq-ico">🚜</div><span class="eq-nm">{{ $eq['name'] }}</span></div></td>
              <td><strong>{{ $eq['total_bookings'] }}</strong></td>
              <td style="color:var(--sky);font-weight:700;font-family:var(--mono)">C${{ number_format(($eq['card_revenue']??0)/100,2) }}</td>
              <td style="color:var(--teal);font-weight:700;font-family:var(--mono)">C${{ number_format(($eq['cash_revenue']??0)/100,2) }}</td>
              <td style="color:var(--gold);font-weight:700;font-family:var(--mono)">C${{ number_format($eq['revenue']/100,2) }}</td>
              <td><div class="ut-wrap"><div class="ut-track"><div class="ut-fill" style="width:{{ $eq['utilization'] }}%"></div></div><span class="ut-pct">{{ $eq['utilization'] }}%</span></div></td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <p style="text-align:center;padding:40px;color:var(--text3);font-size:0.8rem">No equipment data yet.</p>
        @endif
      </div>

    </div>{{-- /r2 --}}

    {{-- ══ ROW 3: TRANSACTIONS ══ --}}
    <div class="sec-label a3">
      RECENT TRANSACTIONS
      <a href="{{ route('admin.dashboard') }}" class="sec-link">View all →</a>
    </div>

    <div class="tx-grid a3">
      @foreach($recentBookings as $b)
        @php
          $bmap=['confirmed'=>'b-confirmed','awaiting_admin'=>'b-awaiting','pending'=>'b-pending','completed'=>'b-completed','cancelled'=>'b-cancelled','rejected'=>'b-rejected'];
          $lmap=['confirmed'=>'Confirmed','awaiting_admin'=>'Paid – Pending','pending'=>'Pending','completed'=>'Completed','cancelled'=>'Cancelled','rejected'=>'Rejected'];
          $bc=$bmap[$b->status]??'b-pending'; $bl=$lmap[$b->status]??ucfirst($b->status);
        @endphp
        <a href="{{ route('admin.booking-show',$b) }}" class="tx-row">
          <div class="tx-left">
            <div class="tx-av">{{ strtoupper(substr($b->user->name??'U',0,1)) }}</div>
            <div class="tx-info">
              <div class="tx-name">#{{ $b->id }} — {{ $b->user->name??'N/A' }}</div>
              <div class="tx-meta">{{ $b->forklift->name??'N/A' }} · {{ $b->created_at?->setTimezone('America/Regina')->format('M d, h:i A') }}</div>
            </div>
          </div>
          <div class="tx-right">
            <span class="tx-amt {{ $b->payment_method }}">{{ $b->payment_method==='card'?'C$'.number_format(($b->amount_total??0)/100,2):'Cash' }}</span>
            <span class="badge {{ $bc }}">{{ $bl }}</span>
          </div>
        </a>
      @endforeach
    </div>

  </div>{{-- /rp-body --}}
</div>{{-- /rp-shell --}}

@endsection
