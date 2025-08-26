@php
  // expected vars: $code (int), $title (string), $message (string), $ctaUrl (string|null), $ctaText (string|null)
  $code    = $code ?? 500;
  $title   = $title ?? 'Something went wrong';
  $message = $message ?? 'An unexpected error occurred.';
  $ctaUrl  = $ctaUrl ?? url('/');
  $ctaText = $ctaText ?? 'Go Home';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $code }} • {{ $title }}</title>

<!-- Inline styles for reliability -->
<style>
  :root {
    --bg1: #f3e8ff;
    --bg2: #e0e7ff;
    --ink: #1e1b4b;
    --muted: #6b7280;
    --indigo: #5b21b6;
    --purple: #a855f7;
    --red: #dc2626;
    --ring: rgba(139, 92, 246, 0.3);
    --glow: rgba(168, 85, 247, 0.4);
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body { height: 100%; }
  body {
    font-family: 'Inter', ui-sans-serif, -apple-system, Segoe UI, Roboto, sans-serif;
    color: var(--ink);
    background: linear-gradient(135deg, var(--bg1) 0%, var(--bg2) 100%);
    overflow: hidden;
    position: relative;
  }
  .wrap {
    min-height: 100%;
    display: grid;
    place-items: center;
    padding: 2rem;
    position: relative;
    z-index: 1;
  }
  .card {
    position: relative;
    width: min(900px, 95vw);
    padding: 2.5rem;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(12px) saturate(160%);
    border: 1px solid rgba(255, 255, 255, 0.7);
    box-shadow: 0 20px 60px rgba(79, 70, 229, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
  }
  .card:hover {
    transform: translateY(-5px) rotate(1deg);
    box-shadow: 0 30px 80px rgba(79, 70, 229, 0.25);
  }
  .glow::before {
    content: "";
    position: absolute;
    inset: -20%;
    background: radial-gradient(400px 300px at var(--mx, 50%) var(--my, 50%), var(--glow), transparent 60%);
    pointer-events: none;
    opacity: 0.7;
    transition: opacity 0.4s ease;
  }
  .glow:hover::before { opacity: 1; }
  .row {
    display: flex;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
  }
  .code {
    width: 130px;
    height: 130px;
    border-radius: 28px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    color: #fff;
    font-weight: 900;
    font-size: 48px;
    letter-spacing: 2px;
    background: linear-gradient(135deg, var(--indigo), var(--purple));
    box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.3);
    position: relative;
    animation: pulse 2s infinite ease-in-out;
  }
  .code::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 28px;
    border: 2px solid rgba(255, 255, 255, 0.4);
  }
  .content { flex: 1 1 auto; min-width: 280px; }
  h1 {
    margin: 0 0 0.5rem;
    font-size: clamp(28px, 4vw, 42px);
    line-height: 1.1;
    font-weight: 800;
    background: linear-gradient(90deg, var(--indigo), var(--purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  p {
    margin: 0.5rem 0;
    color: var(--muted);
    font-size: 16px;
    line-height: 1.5;
  }
  .actions {
    margin-top: 1.5rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    border: none;
    cursor: pointer;
    border-radius: 14px;
    padding: 0.9rem 1.5rem;
    font-weight: 600;
    font-size: 15px;
    text-decoration: none;
    background: linear-gradient(90deg, var(--indigo), var(--purple));
    color: #fff;
    box-shadow: 0 5px 20px rgba(79, 70, 229, 0.3);
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.3s ease;
  }
  .btn:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 30px rgba(79, 70, 229, 0.5);
    background: linear-gradient(90deg, var(--purple), var(--indigo));
  }
  .btn-outline {
    background: transparent;
    color: var(--ink);
    border: 2px solid rgba(79, 70, 229, 0.3);
    box-shadow: none;
  }
  .btn-outline:hover {
    background: rgba(79, 70, 229, 0.1);
    border-color: var(--purple);
    box-shadow: 0 5px 20px rgba(79, 70, 229, 0.2);
  }
  .pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.8rem;
    border-radius: 999px;
    background: rgba(79, 70, 229, 0.15);
    color: var(--purple);
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
    border: 1px solid rgba(79, 70, 229, 0.3);
    animation: fadeIn 0.5s ease;
  }
  .hint {
    font-size: 13px;
    color: var(--muted);
    margin-top: 1rem;
  }
  /* Particle background */
  .particles {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
    z-index: 0;
  }
  .particle {
    position: absolute;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(168, 85, 247, 0.5), transparent 70%);
    animation: float 10s infinite ease-in-out;
  }
  .p1 { width: 50px; height: 50px; left: 10%; top: 20%; animation-delay: 0s; }
  .p2 { width: 80px; height: 80px; right: 15%; top: 30%; animation-delay: 2s; }
  .p3 { width: 60px; height: 60px; left: 40%; bottom: 10%; animation-delay: 4s; }
  .p4 { width: 70px; height: 70px; right: 25%; bottom: 15%; animation-delay: 6s; }
  @keyframes float {
    0%, 100% { transform: translateY(0) scale(1); opacity: 0.5; }
    50% { transform: translateY(-50px) scale(1.1); opacity: 0.8; }
  }
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @media (prefers-reduced-motion: reduce) {
    .card, .btn, .btn-outline, .particle, .code { animation: none; transition: none; }
  }
  @media (max-width: 600px) {
    .card { padding: 1.5rem; }
    .code { width: 100px; height: 100px; font-size: 36px; }
    h1 { font-size: clamp(24px, 5vw, 32px); }
    .row { gap: 1.5rem; }
  }
</style>
</head>
<body>
<div class="particles">
  <span class="particle p1"></span>
  <span class="particle p2"></span>
  <span class="particle p3"></span>
  <span class="particle p4"></span>
</div>
<div class="wrap">
  <div class="card glow" onmousemove="(e=>{const r=e.currentTarget.getBoundingClientRect();e.currentTarget.style.setProperty('--mx', ((e.clientX-r.left)/r.width*100)+'%');e.currentTarget.style.setProperty('--my', ((e.clientY-r.top)/r.height*100)+'%')})(event)">
    <div class="row">
      <div class="code">{{ $code }}</div>
      <div class="content">
        <span class="pill">
          @if($code==403) Forbidden
          @elseif($code==404) Not Found
          @elseif($code==419) Page Expired
          @elseif($code==429) Too Many Requests
          @elseif($code==500) Server Error
          @elseif($code==503) Service Unavailable
          @else Error
          @endif
        </span>
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
        <div class="actions">
          <a href="{{ $ctaUrl }}" class="btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m3 12 9-9 9 9"/><path d="M9 21V9h6v12"/>
            </svg>
            {{ $ctaText }}
          </a>
          <a href="javascript:location.reload()" class="btn btn-outline">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 3v6h6"/>
            </svg>
            Try Again
          </a>
        </div>
        <p class="hint">
          If the problem persists, contact support and mention code <strong>{{ $code }}</strong>.
        </p>
      </div>
    </div>
  </div>
</div>
</body>
</html>