<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'ProjectPilot') }} — Sign In</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap');

  :root{
    --navy-950:#070c1a;
    --navy-900:#0b1330;
    --navy-800:#111c42;
    --teal-400:#3fd8c4;
    --teal-500:#22b8a6;
    --gold-400:#e8bf6a;
    --gold-500:#d1a84a;
    --ink-100:#eef1fb;
    --ink-400:#9aa4c7;
    --border-glass:rgba(232,191,106,0.18);
  }

  *{box-sizing:border-box;margin:0;padding:0;}

  html,body{height:100%;}

  body{
    font-family:'Inter',sans-serif;
    background:
      radial-gradient(circle at 15% 20%, rgba(63,216,196,0.14), transparent 45%),
      radial-gradient(circle at 85% 80%, rgba(209,168,74,0.12), transparent 50%),
      linear-gradient(160deg, var(--navy-950) 0%, var(--navy-900) 55%, var(--navy-800) 100%);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:24px;
    color:var(--ink-100);
    position:relative;
    overflow:hidden;
  }

  /* subtle grid texture */
  body::before{
    content:"";
    position:absolute;
    inset:0;
    background-image:
      linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
    background-size:44px 44px;
    pointer-events:none;
  }

  .stage{
    position:relative;
    width:100%;
    max-width:920px;
    display:grid;
    grid-template-columns:1.1fr 1fr;
    border-radius:22px;
    overflow:hidden;
    border:1px solid var(--border-glass);
    box-shadow:0 30px 80px rgba(0,0,0,0.5);
  }

  /* Left panel: brand / product framing */
  .brand{
    background:linear-gradient(200deg, rgba(34,184,166,0.16), rgba(11,19,48,0.4));
    padding:52px 44px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    border-right:1px solid var(--border-glass);
    backdrop-filter:blur(6px);
  }

  .mark{
    display:flex;
    align-items:center;
    gap:12px;
  }

  .mark-icon{
    width:38px;height:38px;
    border-radius:10px;
    background:linear-gradient(135deg, var(--teal-400), var(--gold-400));
    display:flex;align-items:center;justify-content:center;
    font-family:'Sora',sans-serif;
    font-weight:700;
    color:var(--navy-950);
    font-size:17px;
  }

  .mark-name{
    font-family:'Sora',sans-serif;
    font-weight:600;
    font-size:19px;
    letter-spacing:0.01em;
  }

  .brand-copy{
    margin-top:56px;
  }

  .brand-copy h1{
    font-family:'Sora',sans-serif;
    font-weight:700;
    font-size:32px;
    line-height:1.25;
    color:var(--ink-100);
    max-width:340px;
  }

  .brand-copy p{
    margin-top:16px;
    color:var(--ink-400);
    font-size:14.5px;
    line-height:1.7;
    max-width:320px;
  }

  .board-strip{
    margin-top:40px;
    display:flex;
    flex-direction:column;
    gap:10px;
  }

  .board-row{
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px 12px;
    border-radius:10px;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.06);
  }

  .board-dot{
    width:8px;height:8px;border-radius:50%;
    flex-shrink:0;
  }
  .board-dot.teal{background:var(--teal-400);}
  .board-dot.gold{background:var(--gold-400);}
  .board-dot.dim{background:#4a5578;}

  .board-row span{
    font-size:12.5px;
    color:var(--ink-400);
  }

  .board-row .pct{
    margin-left:auto;
    font-family:'Sora',sans-serif;
    font-size:12px;
    color:var(--ink-100);
  }

  /* Right panel: the form */
  .panel{
    background:rgba(10,16,38,0.55);
    backdrop-filter:blur(18px);
    -webkit-backdrop-filter:blur(18px);
    padding:52px 44px;
    display:flex;
    flex-direction:column;
    justify-content:center;
  }

  .panel h2{
    font-family:'Sora',sans-serif;
    font-weight:600;
    font-size:24px;
  }

  .panel .sub{
    margin-top:8px;
    font-size:13.5px;
    color:var(--ink-400);
  }

  form{
    margin-top:32px;
    display:flex;
    flex-direction:column;
    gap:18px;
  }

  .field label{
    display:block;
    font-size:12.5px;
    color:var(--ink-400);
    margin-bottom:7px;
    letter-spacing:0.01em;
  }

  .field input{
    width:100%;
    background:rgba(255,255,255,0.05);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:10px;
    padding:12px 14px;
    font-size:14.5px;
    color:var(--ink-100);
    outline:none;
    transition:border-color .18s ease, background .18s ease;
  }

  .field input::placeholder{color:#5c6790;}

  .field input:focus{
    border-color:var(--teal-400);
    background:rgba(63,216,196,0.06);
  }

  .row-between{
    display:flex;
    align-items:center;
    justify-content:space-between;
    font-size:12.5px;
  }

  .remember{
    display:flex;
    align-items:center;
    gap:8px;
    color:var(--ink-400);
    cursor:pointer;
  }

  .remember input{accent-color:var(--teal-500); cursor:pointer;}

  .row-between a{
    color:var(--gold-400);
    text-decoration:none;
  }
  .row-between a:hover{text-decoration:underline;}

  button.submit{
    margin-top:6px;
    width:100%;
    padding:13px;
    border:none;
    border-radius:10px;
    background:linear-gradient(120deg, var(--teal-500), var(--gold-500));
    color:var(--navy-950);
    font-family:'Sora',sans-serif;
    font-weight:600;
    font-size:14.5px;
    cursor:pointer;
    transition:filter .15s ease, transform .15s ease;
  }

  button.submit:hover{filter:brightness(1.08);}
  button.submit:active{transform:scale(0.99);}

  button.submit:focus-visible,
  .field input:focus-visible,
  .row-between a:focus-visible{
    outline:2px solid var(--teal-400);
    outline-offset:2px;
  }

  .error{
    font-size:12.5px;
    color:#f0a3a3;
    background:rgba(240,70,70,0.08);
    border:1px solid rgba(240,70,70,0.25);
    padding:9px 12px;
    border-radius:8px;
    margin-top:16px;
  }

  .status-msg{
    font-size:12.5px;
    color:var(--teal-400);
    background:rgba(63,216,196,0.08);
    border:1px solid rgba(63,216,196,0.25);
    padding:9px 12px;
    border-radius:8px;
    margin-top:16px;
  }

  .field-error {
    font-size:11.5px;
    color:#f0a3a3;
    margin-top:4px;
  }

  .foot{
    margin-top:26px;
    text-align:center;
    font-size:12.5px;
    color:var(--ink-400);
  }

  @media (max-width:760px){
    .stage{grid-template-columns:1fr;}
    .brand{display:none;}
    .panel{padding:40px 28px;}
  }

  @media (prefers-reduced-motion: reduce){
    *{transition:none !important;}
  }
</style>
</head>
<body>

<div class="stage">
  <div class="brand">
    <div class="mark">
      <div class="mark-icon">P</div>
      <div class="mark-name">ProjectPilot</div>
    </div>

    <div class="brand-copy">
      <h1>Every project, tracked in one place.</h1>
      <p>Sign in to view your team's boards, deadlines, and progress across all active work.</p>

      <div class="board-strip">
        <div class="board-row">
          <span class="board-dot teal"></span>
          <span>RIMS Portal Rollout</span>
          <span class="pct">72%</span>
        </div>
        <div class="board-row">
          <span class="board-dot gold"></span>
          <span>Vendor Management Sprint</span>
          <span class="pct">45%</span>
        </div>
        <div class="board-row">
          <span class="board-dot dim"></span>
          <span>Tender Page Refresh</span>
          <span class="pct">12%</span>
        </div>
      </div>
    </div>
  </div>

  <div class="panel">
    <h2>Sign in</h2>
    <p class="sub">Enter your credentials to access your workspace.</p>

    <!-- Session Status -->
    @if (session('status'))
      <div class="status-msg">
        {{ session('status') }}
      </div>
    @endif

    <!-- Error Messages -->
    @if ($errors->any())
      <div class="error">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required autofocus autocomplete="username">
        @error('email')
          <div class="field-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
        @error('password')
          <div class="field-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="row-between">
        <label class="remember">
          <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
          Remember me
        </label>
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}">Forgot password?</a>
        @endif
      </div>

      <button type="submit" class="submit">Sign in</button>
    </form>

    <p class="foot">
      @if (Route::has('register'))
        Don't have access? <a href="{{ route('register') }}" style="color:var(--teal-400);text-decoration:none;">Create an account</a>
      @else
        Don't have access? Contact your project admin.
      @endif
    </p>
  </div>
</div>

</body>
</html>
