<link rel="stylesheet" href="/public/css/account.css">
<script type="text/javascript" src="/public/js/qrcode.min.js"></script>
<div class="section mfa">
    <div class="header"><?=$subtitle?></div>
    <form method='post'>
        <div class="input qrcode">
            <div class="element">
                <div class='secret'><?=$values['secret']?></div>
                <div id="qrcode"></div>
            </div>
            <div class="info">Please scan the QR Code to the left.<br/>It will generate your future OTP KEYS.</div>
        </div>
        <div class="input<?=strlen($errors['password']) ? " error" : ""?>">
            <div class="element">
                <label for="password">Password</label>
                <input type='password' id="password" name='password' maxlength='16' value='<?=$values['password']?>'>
            </div>
            <div class="info">Between 4-16 alphanumeric characters.<br/>Allowed characters: . ! @ # $ % ^ & * _ - +</div>
            <div class="warning"><?=$errors['password']?></div>
        </div>
        <div class="input<?=strlen($errors['code']) ? " error" : ""?>">
            <div class="element">
                <label for="code">OTP</label>
                <input type='text' id="code" name='code' maxlength='<?=OTP_TOKEN_LENGTH?>' value='<?=$values['code']?>'>
            </div>
            <div class="info">Exactly <?=OTP_TOKEN_LENGTH?> numeric / alphanumeric characters.</div>
            <div class="warning"><?=$errors['code']?></div>
        </div>
        <div class="input">
            <div class="button">
                <input type='hidden' id='secret' name='secret' maxlength='16' value='<?=$values['secret']?>'>
                <input type='submit' value='Enable'/>
            </div>
        </div>
    </form>
</div>
<script>
    const qrcode = new QRCode('qrcode', {
        text: '<?=$values['qrcode']?>',
        width: 150,
        height: 150,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
    document.addEventListener("DOMContentLoaded", () => {
        const element = document.querySelector("#qrcode");
        const canvas = element.querySelectorAll("canvas")[0];
        element.removeAttribute('title');
        element.removeChild(canvas);
    });
</script>