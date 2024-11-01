<div id="ticketSystemForm">
    <div style="font-size:14px;padding:0 0 15px 0;"><strong><a href="./">Open a New Ticket</a></strong></div>
    <div style="font-size:14px;"><strong>Log In</strong></div>
    <div id="td_tts_errorMessages"></div>
    <div class="td_tts_formField">
        Email:<br />
        <input type="text" name="email" id="email" value="[%email%]" size="45" />
    </div>
    <div class="td_tts_formField">
        Message ID:<br />
        <input type="text" name="messID" id="messID" value="[%messID%]" size="45" />
    </div>
    <div>
        <input type="hidden" name="returnURL" id="returnURL" value="[%returnURL%]" />
        <input type=submit value="Log In" onclick="td_tts_userLogin();" />
    </div>
</div>