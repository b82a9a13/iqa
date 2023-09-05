//Variables and function used to create the assign iqa form
const assignDiv = $('#assign_iqa_div')[0];
const assignError = $('#assign_error')[0];
const assignSuccess = $('#assign_success')[0];
const assignChoose = $('#choose_au')[0];
$('#assign_iqa_btn')[0].addEventListener('click', ()=>{
    if(assignDiv.style.display == 'block'){
        assignDiv.style.display = 'none';
    } else if(assignDiv.style.display == 'none'){
        assignChoose.innerHTML = "<option disabled value='' selected>Choose a User</option>";
        assignError.style.display = 'none';
        assignSuccess.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/admin_assign_iqa_render.inc.php', false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    assignError.innerText = text['error'];
                    assignError.style.display = 'block';
                } else if(text['return']){
                    assignChoose.innerHTML = text['return'];
                } else {
                    assignError.innerText = 'No users available';
                    assignError.style.display = 'block';
                }
            } else {
                assignError.innerText = 'Loading error';
                assignError.style.display = 'block';
            }
        }
        xhr.send();
        assignDiv.style.display = 'block';
    }
});
//Function used to submit assign iqa form
$('#assign_iqa_form')[0].addEventListener('submit', (e)=>{
    e.preventDefault();
    const input = $('#choose_au')[0];
    if(input.value == ''){
        assignError.innerText = 'No input provided';
        assignError.style.display = 'block';
    } else{
        assignError.style.display = 'none';
        assignSuccess.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/admin_assign_iqa.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    assignError.innerText = text['error'];
                    assignError.style.display = 'block';
                } else if(text['return']){
                    assignSuccess.innerText = 'Success';
                    $("#choose_au option[value='"+input.value+"']")[0].remove();
                    $("#choose_au option[value='']").prop('selected', true);
                    assignSuccess.style.display = 'block';
                } else {
                    assignError.innerText = 'Submit error';
                    assignError.style.display = 'block';
                }
            } else {
                assignError.innerText = 'Connection error';
                assignError.style.display = 'block';
            }
        }
        xhr.send(`id=${input.value}`);
    }
});
//Variables and function used for view iqa button and div
const viewDiv = $('#view_iqa_div')[0];
const viewError = $('#view_error')[0];
const viewContent = $('#view_iqa_content')[0];
$('#view_iqa_btn')[0].addEventListener('click', ()=>{
    if(viewDiv.style.display == 'block'){
        viewDiv.style.display = 'none';
    } else if(viewDiv.style.display == 'none'){
        viewError.style.display = 'none';
        viewContent.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/admin_view_iqa_render.inc.php', false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    viewError.innerText = text['error'];
                    viewError.style.display = 'block';
                } else if(text['return']){
                    viewContent.innerHTML = text['return'];
                    viewContent.style.display = 'block';
                } else {
                    viewError.innerText = 'No data available';
                    viewError.style.display = 'block';
                }
            } else {
                viewError.innerText = 'Connection error';
                viewError.style.display = 'block';
            }
        }
        xhr.send();
        viewDiv.style.display = 'block';
    }
});
//Variables and function are used to render the remove iqa form
const removeDiv = $('#remove_iqa_div')[0];
const removeError = $('#remove_error')[0];
const removeSuccess = $('#remove_success')[0];
const removeau = $('#remove_au')[0];
$('#remove_iqa_btn')[0].addEventListener('click', ()=>{
    if(removeDiv.style.display == 'block'){
        removeDiv.style.display = 'none';
    } else if(removeDiv.style.display == 'none'){
        removeau.innerHTML = "<option disabled value='' selected>Choose a User</option>";
        removeError.style.display = 'none';
        removeSuccess.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/admin_remove_iqa_render.inc.php', false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    removeError.innerText = text['error'];
                    removeError.style.display = 'block';
                } else if(text['return']){
                    removeau.innerHTML = text['return'];
                } else {
                    removeError.innerText = 'No users available';
                    removeError.style.display = 'block';
                }
            } else {
                removeError.innerText = 'Connection error';
                removeError.style.display = 'block';
            }
        }
        xhr.send();
        removeDiv.style.display = 'block';
    }
});
$('#remove_iqa_form')[0].addEventListener('submit', (e)=>{
    e.preventDefault();
    const input = $('#remove_au')[0];
    if(input.value == ''){
        removeError.innerText = 'No input provided';
        removeError.style.display = 'block';
    } else{
        removeError.style.display = 'none';
        removeSuccess.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/admin_remove_iqa.inc.php', false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    removeError.innerText = text['error'];
                    removeError.style.display = 'block';
                } else if(text['return']){
                    removeSuccess.innerText = 'Success';
                    $("#remove_au option[value='"+input.value+"']")[0].remove();
                    $("#remove_au option[value='']").prop('selected', true);
                    removeSuccess.style.display = 'block';
                } else {
                    removeError.innerText = 'Submit error';
                    removeError.style.display = 'block';
                }
            } else {
                removeError.innerText = 'Connection error';
                removeError.style.display = 'block';
            }
        }
        xhr.send(`id=${input.value}`);
    }
});