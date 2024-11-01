function td_tts_validateForm() {
    var name = document.getElementById('name').value;
    
    var recaptcha_challenge_field = document.getElementById('recaptcha_challenge_field').value;
    var recaptcha_response_field = document.getElementById('recaptcha_response_field').value;
    
    var errorFlag=0;
    if (name == '') {
        document.getElementById('name').style.backgroundColor='red';
        errorFlag=1;
    }
    var email = document.getElementById('email').value;
    if (email == '') {
        document.getElementById('email').style.backgroundColor='red';
        errorFlag=1;
    }
    var subject = document.getElementById('subject').value;
    if (subject == '') {
        document.getElementById('subject').style.backgroundColor='red';
        errorFlag=1;
    }
    var message = document.getElementById('message').value;
    if (message == '') {
        document.getElementById('message').style.backgroundColor='red';
        errorFlag=1;
    }
    var returnURL = document.getElementById('returnURL').value;
    if (returnURL == '') {
        alert('Something went wrong. Please alert the owner of this site.');
        errorFlag=1;
    }
    if (errorFlag == 1) {
        document.getElementById('td_tts_errorMessages').style.display='block';
        document.getElementById('td_tts_errorMessages').innerHTML = 'Fields in red are required. Please fill them out and submit again.';
        document.getElementById('subButt').value = 'Send Message';
        document.getElementById('subButt').disabled = false;
        return false;
    } else {
        td_tts_userSendMessage(name, email, subject, message, returnURL, recaptcha_challenge_field, recaptcha_response_field);
        return true;
    }    
}
function td_tts_userLogin() {
    var errorFlag=0;
    var email = document.getElementById('email').value;
    if (email == '') {
        document.getElementById('email').style.backgroundColor='red';
        errorFlag=1;
    }
    var messID = document.getElementById('messID').value;
    if (messID == '') {
        document.getElementById('messID').style.backgroundColor='red';
        errorFlag=1;
    }
    var returnURL = document.getElementById('returnURL').value;
    if (returnURL == '') {
        alert('Something went wrong. Please alert the owner of this site.');
        errorFlag=1;
    }
    if (errorFlag == 1) {
        document.getElementById('errorMessages').style.display='block';
        document.getElementById('errorMessages').innerHTML = 'Fields in red are required. Please fill them out and submit again.';
        return false;
    } else {
        td_tts_userLogIn(email, messID, returnURL);
        return true;
    }       
}
