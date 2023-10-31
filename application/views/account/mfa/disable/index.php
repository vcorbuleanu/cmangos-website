<link rel="stylesheet" href="/public/css/account.css">
<div class="section mfa">
    <div class="header"><?=$subtitle?></div>
    <form method='post'>
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
                <label for="code">OTP / RECOVERY KEY</label>
                <input type='text' id="code" name='code' maxlength='<?=OTP_TOKEN_LENGTH?>' value='<?=$values['code']?>'>
            </div>
            <div class="info">Exactly <?=OTP_TOKEN_LENGTH?> numeric / alphanumeric characters.</div>
            <div class="warning"><?=$errors['code']?></div>
        </div>
        <div class="input">
            <div class="button">
                <input type='submit' value='Disable'/>
            </div>
        </div>
    </form>
</div>