@extends('layouts.app')

@section('title', 'Scan QR Code')

@section('content')
<style>
  .scan-wrap{max-width:420px;margin:1.5rem auto;padding:0 1rem}
  .scan-card{background:var(--color-background-primary, #fff);border:0.5px solid var(--color-border-tertiary, #e0e0e0);border-radius:12px;overflow:hidden}
  .scan-header{background:#1d6fa8;padding:1rem 1.25rem;display:flex;align-items:center;gap:10px}
  .scan-header h2{color:#e6f1fb;font-size:16px;font-weight:500;margin:0}
  .scan-body{padding:1.25rem}
  .cam-frame{position:relative;background:#0a0a0a;border-radius:8px;overflow:hidden;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center}
  #reader{width:100%;height:100%}
  #reader video{object-fit:cover!important;width:100%!important;height:100%!important}
  #reader img{display:none!important}
  .corner{position:absolute;width:28px;height:28px;border-color:#378add;border-style:solid;border-width:0;z-index:10}
  .corner.tl{top:10px;left:10px;border-top-width:3px;border-left-width:3px;border-radius:4px 0 0 0}
  .corner.tr{top:10px;right:10px;border-top-width:3px;border-right-width:3px;border-radius:0 4px 0 0}
  .corner.bl{bottom:10px;left:10px;border-bottom-width:3px;border-left-width:3px;border-radius:0 0 0 4px}
  .corner.br{bottom:10px;right:10px;border-bottom-width:3px;border-right-width:3px;border-radius:0 0 4px 0}
  .scan-line{position:absolute;left:10%;right:10%;height:2px;background:linear-gradient(90deg,transparent,#378add,transparent);animation:scanMove 2s ease-in-out infinite;z-index:10}
  @keyframes scanMove{0%{top:15%}50%{top:82%}100%{top:15%}}
  .status-bar{display:flex;align-items:center;gap:8px;margin-top:.75rem;padding:.6rem .75rem;border-radius:8px;background:#f5f5f5;border:0.5px solid #e0e0e0;font-size:13px;color:#555}
  .pulse{width:8px;height:8px;border-radius:50%;background:#1d9e75;flex-shrink:0;animation:pulseAnim 1.4s ease-in-out infinite}
  @keyframes pulseAnim{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.75)}}
  .hint{margin-top:.75rem;font-size:12px;color:#888;text-align:center;line-height:1.5}
  #result-area{margin-top:.75rem}
  .result-card{padding:.875rem 1rem;border-radius:8px;font-size:14px;display:flex;gap:10px;align-items:flex-start;border:0.5px solid}
  .result-card.r-success{background:#eaf3de;border-color:#3b6d11;color:#27500a}
  .result-card.r-error{background:#fcebeb;border-color:#a32d2d;color:#791f1f}
  .result-card.r-info{background:#e6f1fb;border-color:#185fa5;color:#0c447c}
  .result-card.r-warning{background:#faeeda;border-color:#854f0b;color:#633806}
  .result-icon{font-size:20px;line-height:1;flex-shrink:0;margin-top:1px}
  .r-label{font-weight:500;font-size:13px;margin:0;line-height:1.4}
  .r-sub{font-size:12px;opacity:.8;margin:3px 0 0;line-height:1.4}
</style>

<div class="scan-wrap">
  <div class="scan-card">

    <div class="scan-header">
      <i class="fas fa-qrcode" style="font-size:20px;color:#b5d4f4"></i>
      <h2>Scan Attendance QR</h2>
    </div>

    <div class="scan-body">

      <div class="cam-frame" id="cam-frame">
        <div id="reader"></div>
        <div class="corner tl"></div>
        <div class="corner tr"></div>
        <div class="corner bl"></div>
        <div class="corner br"></div>
        <div class="scan-line" id="scan-line"></div>
      </div>

      <div class="status-bar">
        <div class="pulse" id="pulse-dot"></div>
        <span id="status-text">Camera starting…</span>
      </div>

      <p class="hint">
        Point your camera at the QR code on the board.<br>
        First scan = <strong>Time In</strong> &nbsp;·&nbsp; Second scan = <strong>Time Out</strong>
      </p>

      <div id="result-area"></div>

    </div>
  </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
  const statusText = document.getElementById('status-text');
  const pulseDot   = document.getElementById('pulse-dot');
  const scanLine   = document.getElementById('scan-line');
  const resultArea = document.getElementById('result-area');
  let scanning     = true;

  function setStatus(msg, color) {
    statusText.textContent = msg;
    pulseDot.style.background = color || '#1d9e75';
  }

  function showResult(type, iconClass, label, sub) {
    resultArea.innerHTML = `
      <div class="result-card r-${type}">
        <i class="fas fa-${iconClass} result-icon"></i>
        <div>
          <p class="r-label">${label}</p>
          ${sub ? `<p class="r-sub">${sub}</p>` : ''}
        </div>
      </div>`;
  }

  const html5QrCode = new Html5Qrcode('reader');

  function processToken(urlOrToken) {
    let token = urlOrToken;
    if (urlOrToken.includes('/student/attendance/scan/')) {
      const parts = urlOrToken.split('/');
      token = parts[parts.length - 1];
    }

    scanLine.style.animation = 'none';
    setStatus('Verifying…', '#ef9f27');

    fetch('/student/attendance/scan/' + token, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(r => r.text())
    .then(html => {
      if (html.includes('Time In recorded') || html.includes('timein')) {
        setStatus('Time In recorded', '#1d9e75');
        showResult('success', 'sign-in-alt', 'Time In recorded!', 'Redirecting to dashboard…');
        setTimeout(() => window.location.href = '{{ route("student.dashboard") }}', 2800);

      } else if (html.includes('Time Out recorded') || html.includes('timeout')) {
        setStatus('Time Out recorded', '#1d9e75');
        showResult('success', 'sign-out-alt', 'Time Out recorded!', 'Redirecting to dashboard…');
        setTimeout(() => window.location.href = '{{ route("student.dashboard") }}', 2800);

      } else if (html.includes('not registered') || html.includes('Not Registered') || html.includes('not enrolled')) {
        setStatus('Not registered', '#e24b4a');
        showResult('error', 'user-times', 'Not registered for this subject', 'Contact your teacher to be added first.');
        restartAfter(4500);

      } else if (html.includes('Already') || html.includes('already completed')) {
        setStatus('Already recorded', '#185fa5');
        showResult('info', 'calendar-check', 'Attendance already recorded', 'Nothing to do for this session.');
        setTimeout(() => window.location.href = '{{ route("student.dashboard") }}', 2800);

      } else if (html.includes('expired') || html.includes('invalid') || html.includes('Invalid')) {
        setStatus('QR expired', '#e24b4a');
        showResult('error', 'clock', 'QR code expired or invalid', 'Ask your teacher to generate a new one.');
        restartAfter(4500);

      } else {
        setStatus('Try again', '#ef9f27');
        showResult('warning', 'exclamation-triangle', 'Could not process scan', 'Please try again.');
        restartAfter(3000);
      }
    })
    .catch(() => {
      setStatus('Network error', '#e24b4a');
      showResult('error', 'wifi', 'Network error', 'Check your connection and try again.');
      restartAfter(3500);
    });
  }

  function restartAfter(ms) {
    setTimeout(() => {
      scanning = true;
      scanLine.style.animation = 'scanMove 2s ease-in-out infinite';
      resultArea.innerHTML = '';
      startScanner();
    }, ms);
  }

  function onScanSuccess(decoded) {
    if (!scanning) return;
    scanning = false;
    html5QrCode.stop()
      .then(() => processToken(decoded))
      .catch(() => processToken(decoded));
  }

  function startScanner() {
    html5QrCode.start(
      { facingMode: 'environment' },
      {
        fps: 10,
        qrbox: { width: 220, height: 220 },
        rememberLastUsedCamera: true,
        aspectRatio: 1.0
      },
      onScanSuccess,
      () => {}
    )
    .then(() => setStatus('Ready to scan', '#1d9e75'))
    .catch(err => {
      setStatus('Camera unavailable', '#e24b4a');
      showResult('error', 'video-slash', 'Camera access denied', 'Allow camera permission and reload the page.');
    });
  }

  startScanner();
</script>
@endsection