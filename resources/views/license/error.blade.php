<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de Licencia</title>
    <style>
        html, body { height: 100%; }
        body {
            margin: 0;
            background: #0b0e10;
            color: #00ff88;
            font-family: "Fira Code", Consolas, Monaco, "Courier New", monospace;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .terminal {
            width: min(900px, 92vw);
            border: 2px solid #00ff88;
            padding: 20px 24px;
            box-shadow: 0 0 20px rgba(0,255,136,0.2);
        }
        .title { color: #7dfcbe; margin: 0 0 12px; }
        .line { white-space: pre-wrap; line-height: 1.6; }
        .muted { color: #54c99b; }
        .cursor { display: inline-block; width: 10px; background: #00ff88; animation: blink 1s step-start infinite; }
        @keyframes blink { 50% { opacity: 0; } }
        .btn-row { margin-top: 18px; }
        .btn {
            display: inline-block;
            color: #0b0e10;
            background: #00ff88;
            border: none;
            padding: 8px 14px;
            font-weight: 700;
            text-decoration: none;
            margin-right: 10px;
        }
        .btn.alt { background: transparent; color: #7dfcbe; border: 1px solid #7dfcbe; }
        code { color: #a6ffd6; }
    </style>
</head>
<body>
    <div class="terminal" role="alert" aria-live="assertive">
        <pre class="line">┌──────────────────────────────────────────────────────────────┐</pre>
        <pre class="line title">│   TAURANET · Sistema de Restaurante                      │</pre>
        <pre class="line">├──────────────────────────────────────────────────────────────┤</pre>
        <pre class="line">│   Estado: <strong>ERROR</strong>                                           │</pre>
        <pre class="line">│                                                              │</pre>
        <pre class="line">│   Detalle: {!! nl2br(e($message ?? 'Error de licencia.')) !!} @php /* safe */ @endphp            │</pre>
        <pre class="line">│                                                              │</pre>
        <pre class="line">└──────────────────────────────────────────────────────────────┘</pre>
        {{-- <div class="btn-row">
            <a class="btn" href="/">Reintentar</a>
            <a class="btn alt" href="javascript:location.reload()">Recargar</a>
        </div> --}}
        <div class="line" style="margin-top:12px"><span class="muted">$</span> <span class="cursor">&nbsp;</span></div>
    </div>
</body>
</html>
