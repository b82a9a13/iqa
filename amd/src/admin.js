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
//Function is used to retrieve log data dependant on the form data
$('#logs_iqa_filter_form')[0].addEventListener('submit', (e)=>{
    e.preventDefault();
    const error = $('#logs_error')[0];
    const content = $(`#logs_iqa_content`)[0];
    content.style.display = 'none';
    error.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/admin_logs_iqa_render.inc.php', false);
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
    xhr.send(`sd=${$('#startdate')[0].value}&ed=${$('#enddate')[0].value}`);
});
function header_clicked(string, integer){
    if(string == 'view' || string == 'logs'){
        //set the headers of the table to contain the correct arrow showing how the table is sorted
        const headers = $(`#${string}_thead`).find('tr:first th');
        let order = 'asc';
        for(let i = 0; i < headers.length; i++){
            if(i === integer){
                switch (headers[i].getAttribute('sort')){
                    case 'asc':
                        headers[i].setAttribute('sort', 'desc');
                        headers[i].querySelector('span').innerHTML = '&darr;';
                        order = 'desc';
                        break;
                    case 'desc':
                        headers[i].setAttribute('sort', 'asc');
                        headers[i].querySelector('span').innerHTML = '&uarr;';
                        break;
                    case '':
                        headers[i].setAttribute('sort', 'asc');
                        headers[i].querySelector('span').innerHTML = '&uarr;';
                        break;
                }
            } else {
                headers[i].setAttribute('sort', '');
                headers[i].querySelector('span').innerHTML = '';
            }
        }
        //Get all the table data and put it into a array, and only performs the task if there is data within the table to sort
        const body = $(`#${string}_tbody`).find('tr');
        if(body.length > 1){
            let array = [];
            body.each(function(index, row){
                const tds = $(row).find('td');
                let tmpArray = [];
                tds.each(function(tdindex, td){
                    if(/[0-9]/.test(td.innerText) === true && td.innerText.includes('/') === true && /[a-zA-Z]/.test(td.innerText) === false){
                        tmpString = td.innerText.split('/');
                        tmpArray.push([td.getAttribute('dtval'), 'date']);
                    }else if(td.querySelector('a')){
                        tmpArray.push([td.innerText, td.querySelector('a').getAttribute('href')]);
                    } else{
                        tmpArray.push([td.innerText, null]);
                    }
                })
                const tmpData = tmpArray[0];
                tmpArray[0] = tmpArray[integer];
                tmpArray[integer] = tmpData;
                array.push(tmpArray);
            });
            //Sorts the data in the array
            switch (order){
                case 'asc':
                    if(/[0-9]/.test(array[0][0]) === true && /[a-zA-Z]/.test(array[0][0]) === false){
                        array.sort(function(a,b){return parseFloat(a[0]) - parseFloat(b[0])});
                    } else {
                        array.sort(function(a,b){
                            const x = a[0][0];
                            const y = b[0][0];
                            if(x < y){return -1;}
                            if(y < x){return 1;}
                            return 0;
                        });
                    }
                    break;
                case 'desc':
                    array.reverse();
                    break;
            }
            //Rearrange the array to the default arrangement
            let sortedArray = [];
            array.forEach(function(element){
                const tmpData = element[integer];
                element[integer] = element[0];
                element[0] = tmpData;
                sortedArray.push(element);
            });
            //Add the data back to the table
            const tbody = $(`#${string}_tbody`)[0];
            tbody.innerHTML = '';
            sortedArray.forEach(function(element){
                let row = '<tr>';
                for(let i = 0; i < element.length; i++){
                    switch (element[i][1]){
                        case 'date':
                            const dateOrig = element[i][0];
                            element[i][0] = new Date(element[i][0] * 1000);
                            row += `<td dtval='${dateOrig}'>${String(element[i][0].getDate()).padStart(2, '0')}/${String(element[i][0].getMonth() + 1).padStart(2, '0')}/${element[i][0].getFullYear()} ${String(element[i][0].getHours()).padStart(2, '0')}:${String(element[i][0].getMinutes()).padStart(2, '0')}:${String(element[i][0].getSeconds()).padStart(2, '0')}</td>`;
                            break;
                        case null:
                            row += `<td>${element[i][0]}</td>`;
                            break;
                        default:
                            row += `<td><a href='window.location.href=${element[i][1]}'>${element[i][0]}</a></td>`;
                            break;
                    }
                }
                row += '</tr>';
                tbody.innerHTML += row;
            });
        }
    }
}