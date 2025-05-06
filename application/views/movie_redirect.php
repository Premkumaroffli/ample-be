<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($movie->title) ?> - Movie Download</title>
    <meta name="robots" content="noindex,nofollow">
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; }
        .box { background: #f9f9f9; padding: 20px; border-radius: 8px; display: inline-block; }
        .btn { padding: 10px 20px; background: #28a745; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; display: inline-block; }
    </style>
</head>
<body>
    <div class="box">
        <h2>🎬 <?= htmlspecialchars($movie->title) ?></h2>
        <p>Preparing your download link...</p>
        
        <!-- ✅ PLACE YOUR ADS HERE -->
        <div style="margin: 20px 0;">
            <!-- Example Google AdSense -->
            <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script> -->
            <!-- <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-xxxxxxxx"
                 data-ad-slot="xxxxxxx"
                 data-ad-format="auto"></ins> -->
            <!-- <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script> -->
        </div>

        <p>⏳ Wait <span id="timer">5</span> seconds...</p>

        <a id="downloadBtn" class="btn" href="<?= htmlspecialchars($movie->original_url) ?>" style="display:none;">👉 Download Now</a>
    </div>

    <script>
        let count = 5;
        let timer = setInterval(function() {
            count--;
            document.getElementById('timer').innerText = count;
            if (count <= 0) {
                clearInterval(timer);
                document.getElementById('downloadBtn').style.display = 'inline-block';
            }
        }, 1000);
    </script>
</body>
</html>
