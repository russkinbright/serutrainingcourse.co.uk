<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>We’ll be right back</title>
<style>
  :root{--indigo:#4f46e5;--purple:#8b5cf6;--ink:#1f2937;--muted:#6b7280}
  html,body{height:100%}body{margin:0;font-family:ui-sans-serif,system-ui,Segoe UI,Roboto,Inter,Arial;
  color:var(--ink);background:radial-gradient(900px 500px at -10% -10%,#ede9fe,transparent),
  radial-gradient(900px 700px at 110% -10%,#dbeafe,transparent),linear-gradient(180deg,#f5f3ff,#fff)}
  .wrap{min-height:100%;display:grid;place-items:center;padding:2rem}
  .card{position:relative;width:min(800px,92vw);padding:2rem;border-radius:18px;background:rgba(255,255,255,.85);
  backdrop-filter:blur(10px) saturate(140%);border:1px solid rgba(255,255,255,.7);
  box-shadow:0 30px 70px rgba(79,70,229,.12), inset 0 1px 0 rgba(255,255,255,.85);overflow:hidden}
  .b{position:absolute;border-radius:999px;filter:blur(2px);opacity:.7}
  .b1{width:120px;height:120px;left:-30px;top:-30px;background:radial-gradient(circle at 30% 30%,rgba(79,70,229,.35),rgba(79,70,229,.12) 60%,transparent 70%);animation:f 14s linear infinite}
  .b2{width:90px;height:90px;right:-20px;top:20px;background:radial-gradient(circle at 60% 60%,rgba(139,92,246,.3),rgba(139,92,246,.12) 60%,transparent 70%);animation:f 16s linear infinite 1s}
  .b3{width:140px;height:140px;left:40%;bottom:-40px;background:radial-gradient(circle at 40% 40%,rgba(99,102,241,.28),rgba(99,102,241,.1) 60%,transparent 70%);animation:f 18s linear infinite .6s}
  @keyframes f{0%{transform:translateY(0) scale(1)}100%{transform:translateY(-60px) scale(1.05)}}
  h1{margin:0 0 .5rem;font-size:clamp(26px,3.8vw,40px)}
  p{margin:.25rem 0;color:var(--muted)}
  .row{display:flex;gap:1rem;flex-wrap:wrap;align-items:center}
  .code{width:110px;height:110px;border-radius:22px;display:grid;place-items:center;background:linear-gradient(135deg,var(--indigo),var(--purple));color:#fff;font-weight:900;font-size:32px;box-shadow:0 12px 30px rgba(79,70,229,.35)}
  .actions{margin-top:1rem;display:flex;gap:.75rem;flex-wrap:wrap}
  .btn{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1rem;border-radius:12px;border:1px solid rgba(31,41,55,.15);background:#fff;
  color:var(--indigo);text-decoration:none;font-weight:600;box-shadow:0 5px 20px rgba(79,70,229,.12)}
  .btn:hover{transform:translateY(-1px);box-shadow:0 14px 40px rgba(79,70,229,.18)}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <span class="b b1"></span><span class="b b2"></span><span class="b b3"></span>
    <div class="row">
      <div class="code">503</div>
      <div>

        {{-- Dynamic headline/message based on maintenance vs generic 503 --}}
        @if(app()->isDownForMaintenance())
          <h1>We’ll be right back</h1>
          <p>We’re doing some maintenance. Please check back soon.</p>
        @else
          <h1>Something went wrong</h1>
          <p>An unexpected error occurred. Please try again later.</p>
        @endif

        @if(isset($retryAfter) && $retryAfter)
          <p>Retry after: <strong>{{ $retryAfter }}s</strong></p>
        @endif

        <div class="actions">
          <a href="{{ url('/') }}" class="btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 12 9-9 9 9"/><path d="M9 21V9h6v12"/></svg>
            Home
          </a>
          <a href="javascript:location.reload()" class="btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 3v6h6"/></svg>
            Try Again
          </a>
        </div>

      </div>
    </div>
  </div>
</div>
</body>
</html>
