<link rel="stylesheet" href="/public/css/account.css">
<div class="section create">
    <div class="header"><?=$subtitle?></div>
    <form method='post'>
        <div class="input<?=strlen($errors['username']) ? " error" : ""?>">
            <div class="element">
                <label for="username">Username</label>
                <input type='text' id="username" name='username' maxlength='16' value='<?=$values['username']?>'>
            </div>
            <div class="info">Between 4-16 alphanumeric characters.<br/>Allowed characters: . _</div>
            <div class="warning"><?=$errors['username']?></div>
        </div>
        <div class="input<?=strlen($errors['email']) ? " error" : ""?>">
            <div class="element">
                <label for="email">Email</label>
                <input type='text' id="email" name='email' maxlength='32' value='<?=$values['email']?>' placeholder='username@domain'>
            </div>
            <div class="info">Between 4-32 characters.<br/>Allowed characters: _ + - @ .</div>
            <div class="warning"><?=$errors['email']?></div>
        </div>
        <div class="input<?=strlen($errors['password']) ? " error" : ""?>">
            <div class="element">
                <label for="password">Password</label>
                <input type='password' id="password" name='password' maxlength='16' value='<?=$values['password']?>'>
            </div>
            <div class="info">Between 4-16 alphanumeric characters.<br/>Allowed characters: . ! @ # $ % ^ & * _ - +</div>
            <div class="warning"><?=$errors['password']?></div>
        </div>
        <div class="input<?=strlen($errors['password_confirm']) ? " error" : ""?>">
            <div class="element">
                <label for="password_confirm">Confirm Password</label>
                <input type='password' id="password_confirm" name='password_confirm' maxlength='16' value='<?=$values['password_confirm']?>'>
            </div>
            <div class="info">Between 4-16 alphanumeric characters.<br/>Allowed characters: . ! @ # $ % ^ & * _ - +</div>
            <div class="warning"><?=$errors['password_confirm']?></div>
        </div>
        <div class="input">
            <div class="button">
                <input type='submit' value='Create'/>
            </div>
        </div>
    </form>
</div>