<div id="ticketSystemForm">
    <div style="font-size:14px;padding:0 0 15px 0;"><strong>Reply to an existing ticket: <a href="?action=td_tts_ticketLogIn">Log In</a></strong></div>
    <div style="font-size:14px;"><strong>Open a New Ticket</strong></div>
    <div id="td_tts_errorMessages"></div>
    <div class="td_tts_formField">
        Name:<br />
        <input type="text" name="name" id="name" size="45" />
    </div>
    <div class="td_tts_formField">
        Email:<br />
        <input type="text" name="email" id="email" size="45" />
    </div>
    <div class="td_tts_formField">
        Subject:<br />
        <input type="text" name="subject" id="subject" size="45" />
    </div>
    <div class="td_tts_formField">
        Message:<br />
        <textarea name="message" id="message" cols="75" rows="10"></textarea>
    </div>
    <div class="td_tts_formField">
        [%captcha%]        
    </div>
    <div>
        <input type="hidden" name="returnURL" id="returnURL" value="[%returnURL%]" />
        <input type="submit" id="subButt" value="Send Message" onclick="
            this.value = 'Sending...';
            this.disabled = true;
            td_tts_validateForm();
            this.disabled = false;
        " />
    </div>
</div>