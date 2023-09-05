function view_div(string){
    const stringArray = ['assign', 'remove', 'view', 'logs'];
    if(stringArray.includes(string)){
        stringArray.forEach((item)=>{
            if(item != string){
                if($(`#${item}_iqa_div`)[0].style.display == 'block'){
                    $(`#${item}_iqa_div`)[0].style.display = 'none';
                }
            }
        });
    }
}
//function used to render a form and to handle the form submission
function select_form(string){
    if(string == 'assign' || string == 'remove'){
        const div = $(`#${string}_iqa_div`)[0];
        const error = $(`#${string}_error`)[0];
        const success = $(`#${string}_success`)[0];
        const choose = $(`#${string}_au`)[0];
        $(`#${string}_iqa_btn`)[0].addEventListener('click', ()=>{
            view_div(string);
            if(div.style.display == 'block'){
                div.style.display = 'none';
            } else if(div.style.display == 'none'){
                choose.innerHTML = "<option disabled value='' selected>Choose a User</option>";
                error.style.display = 'none';
                success.style.display = 'none';
                const xhr = new XMLHttpRequest();
                xhr.open('POST', `./classes/inc/admin_${string}_iqa_render.inc.php`, false);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function(){
                    if(this.status == 200){
                        const text = JSON.parse(this.responseText);
                        if(text['error']){
                            error.innerText = text['error'];
                            error.style.display = 'block';
                        } else if(text['return']){
                            choose.innerHTML = text['return'];
                        } else {
                            error.innerText = 'no users available';
                            error.style.display = 'block';
                        }
                    } else {
                        error.innerText = 'Loading error';
                        error.style.display = 'block';
                    }
                }
                xhr.send();
                div.style.display = 'block';
            }
        });
        $(`#${string}_iqa_form`)[0].addEventListener('submit', (e)=>{
            e.preventDefault();
            if(choose.value == ''){
                error.innerText = 'No input provided';
                error.style.display = 'block';
            } else {
                error.style.display = 'none';
                success.style.display = 'none';
                const xhr = new XMLHttpRequest();
                xhr.open('POST', `./classes/inc/admin_${string}_iqa.inc.php`, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function(){
                    if(this.status == 200){
                        const text = JSON.parse(this.responseText);
                        if(text['error']){
                            error.innerText = text['error'];
                            error.style.display = 'block';
                        } else if(text['return']){
                            success.innerText = 'Success';
                            $(`#${string}_au option[value='${choose.value}']`)[0].remove();
                            $(`#${string}_au option[value='']`).prop('selected', true);
                            success.style.display = 'block';
                        } else {
                            error.innerText = 'Submit error';
                            error.style.display = 'block';
                        }
                    } else {
                        error.innerText = 'Connection error';
                        error.style.display = 'block';
                    }
                }
                xhr.send(`id=${choose.value}`);
            }
        });
    }
}
select_form('assign');
select_form('remove');
//Function used to render data
function view_data(string){
    if(string == 'view' || string == 'logs'){
        const div = $(`#${string}_iqa_div`)[0];
        const error = $(`#${string}_error`)[0];
        const content = $(`#${string}_iqa_content`)[0];
        $(`#${string}_iqa_btn`)[0].addEventListener('click', ()=>{
            view_div(string);
            if(div.style.display == 'block'){
                div.style.display = 'none';
            } else if(div.style.display == 'none'){
                error.style.display = 'none';
                content.style.display = 'none';
                const xhr = new XMLHttpRequest();
                xhr.open('POST', `./classes/inc/admin_${string}_iqa_render.inc.php`, false);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function(){
                    if(this.status == 200){
                        const text = JSON.parse(this.responseText);
                        if(text['error']){
                            error.innerText = text['error'];
                            error.style.display = 'block';
                        } else if(text['return']){
                            content.innerHTML = text['return'];
                            content.style.display = 'block';
                        } else {
                            error.innerText = 'No data available';
                            error.style.display = 'block';
                        }
                    } else {
                        error.innerText = 'Connection error';
                        error.style.display = 'block';
                    }
                }
                switch (string){
                    case 'logs':
                        xhr.send(`sd=${$('#startdate')[0].value}&ed=${$('#enddate')[0].value}`);
                        break;
                    default:
                        xhr.send();
                }
                div.style.display = 'block';
            }
        });
    }
}
view_data('view');
view_data('logs');
$('#logs_iqa_filter_form').addEventListener('submit', (e)=>{
    e.preventDefault();
    
});