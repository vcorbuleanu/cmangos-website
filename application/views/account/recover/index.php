<link rel="stylesheet" href="/public/css/account.css">
<div class="section recover">
    <div class="header"><?=$subtitle?></div>
    <form method='post'>
        <div class="input<?=strlen($errors['email']) ? " error" : ""?>">
            <div class="element">
                <label for="email">Email</label>
                <input type='text' id="email" name='email' maxlength='32' value='<?=$values['email']?>' placeholder='username@domain'>
            </div>
            <div class="info">Between 4-32 characters.<br/>Allowed characters: _ + - @ .</div>
            <div class="warning"><?=$errors['email']?></div>
        </div>
        <div class="input">
            <div class="button">
                <input type='submit' value='Recover'/>
            </div>
        </div>
    </form>
</div>