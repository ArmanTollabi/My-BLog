<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>404 — صفحه پیدا نشد</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;1,400&family=Vazirmatn:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --ink:#12181F;
    --line:#2E3742;
    --amber:#E0A63A;
    --paper:#F1EDE4;
    --muted:#8B93A0;
  }
  *{box-sizing:border-box;margin:0;padding:0;}
  html,body{height:100%;}
  body{
    background:var(--ink);
    color:var(--paper);
    font-family:'Vazirmatn',sans-serif;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:32px;
    overflow:hidden;
  }
  .scene{
    display:flex;
    align-items:center;
    gap:64px;
    max-width:820px;
    width:100%;
  }
  .gate-wrap{
    flex-shrink:0;
    position:relative;
  }
  .code{
    font-family:'Newsreader',serif;
    font-style:italic;
    font-weight:400;
    font-size:15vw;
    line-height:1;
    color:var(--paper);
    letter-spacing:-0.02em;
    position:absolute;
    top:50%;
    left:62%;
    transform:translate(-50%,-54%);
    pointer-events:none;
    white-space:nowrap;
  }
  .content{
    flex:1;
    min-width:0;
  }
  .eyebrow{
    font-size:14px;
    color:var(--amber);
    margin-bottom:14px;
  }
  h1{
    font-family:'Newsreader',serif;
    font-weight:500;
    font-size:32px;
    line-height:1.3;
    color:var(--paper);
    margin-bottom:16px;
  }
  p{
    font-size:16px;
    line-height:1.8;
    color:var(--muted);
    max-width:36ch;
    margin-bottom:32px;
  }
  .actions{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
  }
  a.btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    text-decoration:none;
    padding:13px 26px;
    border-radius:999px;
    font-size:15px;
    font-weight:500;
    transition:all .2s ease;
  }
  a.btn.primary{
    background:var(--amber);
    color:#241a06;
  }
  a.btn.primary:hover{
    background:#eeb84f;
  }
  a.btn.ghost{
    background:transparent;
    color:var(--paper);
    border:1px solid var(--line);
  }
  a.btn.ghost:hover{
    border-color:var(--muted);
  }
  @media (max-width:680px){
    .scene{flex-direction:column;text-align:center;gap:12px;}
    .gate-wrap{width:100%;max-width:280px;}
    .code{position:static;transform:none;font-size:96px;margin-bottom:8px;}
    p{max-width:none;margin-inline:auto;}
    .actions{justify-content:center;}
  }
</style>
</head>
<body>

  <div class="scene">
    <div class="gate-wrap">
      <svg width="220" height="260" viewBox="0 0 220 260" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="30" y="20" width="160" height="220" rx="2" stroke="#3A4453" stroke-width="1.5" stroke-dasharray="4 6"/>
        <line x1="30" y1="20" x2="190" y2="20" stroke="#3A4453" stroke-width="1.5" stroke-dasharray="4 6"/>
        <path d="M30 20 Q110 -6 190 20" stroke="#3A4453" stroke-width="1.5" fill="none" stroke-dasharray="4 6"/>
        <line x1="30" y1="20" x2="30" y2="240" stroke="#3A4453" stroke-width="1.5" stroke-dasharray="4 6"/>
        <line x1="190" y1="20" x2="190" y2="240" stroke="#3A4453" stroke-width="1.5" stroke-dasharray="4 6"/>
        <line x1="70" y1="60" x2="150" y2="200" stroke="#E0A63A" stroke-width="1.5"/>
        <line x1="150" y1="60" x2="70" y2="200" stroke="#E0A63A" stroke-width="1.5"/>
      </svg>
      <div class="code">404</div>
    </div>

    <div class="content">
      <div class="eyebrow">صفحه پیدا نشد</div>
      <h1>این‌جا چیزی برای دیدن نیست</h1>
      <p>
        آدرسی که دنبالش بودی یا هیچ‌وقت وجود نداشته، یا جابه‌جا شده. یک بار دیگه آدرس را بررسی کن یا از همین‌جا برگرد.
      </p>
      <div class="actions">
        <a href="/posts" class="btn primary">بازگشت به پست‌ها</a>
        <a href="javascript:history.back()" class="btn ghost">برگشت به صفحه قبل</a>
      </div>
    </div>
  </div>

</body>
</html>
