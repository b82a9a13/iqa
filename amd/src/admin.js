function view_div(string, type){
    const stringArray = ['assign', 'remove', 'view', 'logs'];
    if(stringArray.includes(string) && (type == 'iqa' || type == 'course' || type == 'learner')){
        stringArray.forEach((item)=>{
            if(item != string){
                if($(`#${item}_${type}_div`)[0].style.display == 'block'){
                    $(`#${item}_${type}_div`)[0].style.display = 'none';
                }
            }
        });
    }
}
//function used to render a form and to handle the form submission
function select_form(string, type){
    if((string == 'assign' || string == 'remove') && (type == 'iqa' || type == 'course' || type == 'learner')){
        const div = $(`#${string}_${type}_div`)[0];
        const error = $(`#${string}_${type}_error`)[0];
        const success = $(`#${string}_${type}_success`)[0];
        const content = $(`#${string}_${type}_content`)[0];
        $(`#${string}_${type}_btn`)[0].addEventListener('click', ()=>{
            const choose = $(`#${string}_${type}_au`)[0];
            view_div(string, type);
            if(div.style.display == 'block'){
                div.style.display = 'none';
            } else if(div.style.display == 'none'){
                switch(type){
                    case 'iqa':
                        choose.innerHTML = `<option disabled value='' selected>Choose a User</option>`;
                        break;
                    case 'course':
                        choose.innerHTML = `<option disabled value='' selected>Choose a Course</option>`;
                        break;
                    case 'learner':
                        switch(string){
                            case 'remove':
                                choose.innerHTML = `<option disabled value='' selected>Choose a Assignment to remove</option>`;
                                break;
                            case 'assign':
                                choose.innerHTML = `<option disabled value='' selected>Choose a Course</option>`;
                                break;
                        }
                        break;
                }
                error.style.display = 'none';
                success.style.display = 'none';
                const xhr = new XMLHttpRequest();
                xhr.open('POST', `./classes/inc/admin_${string}_render.inc.php`, false);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function(){
                    if(this.status == 200){
                        const text = JSON.parse(this.responseText);
                        if(text['error']){
                            error.innerText = text['error'];
                            error.style.display = 'block';
                        } else if(text['return']){
                            content.innerHTML = text['return'];
                        } else {
                            error.innerText = 'no data available';
                            error.style.display = 'block';
                        }
                    } else {
                        error.innerText = 'Loading error';
                        error.style.display = 'block';
                    }
                }
                xhr.send(`t=${type}`);
                div.style.display = 'block';
            }
        });
        $(`#${string}_${type}_form`)[0].addEventListener('submit', (e)=>{
            e.preventDefault();
            const choose = $(`#${string}_${type}_au`)[0];
            if(choose.value == ''){
                error.innerText = 'No input provided';
                error.style.display = 'block';
            } else {
                error.style.display = 'none';
                success.style.display = 'none';
                const xhr = new XMLHttpRequest();
                xhr.open('POST', `./classes/inc/admin_${string}.inc.php`, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function(){
                    if(this.status == 200){
                        const text = JSON.parse(this.responseText);
                        if(text['error']){
                            error.innerText = text['error'];
                            error.style.display = 'block';
                        } else if(text['return']){
                            success.innerText = 'Success';
                            switch(type){
                                case 'learner':
                                    switch(string){
                                        case 'assign':
                                            $(`#${string}_${type}_au2 option[value='${$(`#${string}_${type}_au2`)[0].value}']`)[0].remove();
                                            $(`#${string}_${type}_au2 option[value='']`).prop('selected', true);
                                            break;
                                        case 'remove':
                                            $(`#${string}_${type}_au option[value='${choose.value}']`)[0].remove();
                                            $(`#${string}_${type}_au option[value='']`).prop('selected', true);
                                            break;
                                    }
                                    break;
                                default:
                                    $(`#${string}_${type}_au option[value='${choose.value}']`)[0].remove();
                                    $(`#${string}_${type}_au option[value='']`).prop('selected', true);
                                    break;
                            }
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
                switch(type){
                    case 'learner':
                        switch(string){
                            case 'assign':
                                xhr.send(`id=${choose.value}&t=${type}&l=${$(`#${string}_${type}_au2`)[0].value}&i=${$(`#${string}_${type}_au3`)[0].value}`);
                                break;
                            case 'remove':
                                xhr.send(`id=${choose.value}&t=${type}`);
                                break;
                        }
                        break;
                    default:
                        xhr.send(`id=${choose.value}&t=${type}`);
                        break;
                }
            }
        });
    }
}
select_form('assign', 'iqa');
select_form('remove', 'iqa');
select_form('assign', 'course');
select_form('remove', 'course');
select_form('assign', 'learner');
select_form('remove', 'learner');
//Function used to render data
function view_data(string, type){
    if((string == 'view' || string == 'logs') && (type == 'iqa' || type == 'course' || type == 'learner')){
        const div = $(`#${string}_${type}_div`)[0];
        const error = $(`#${string}_${type}_error`)[0];
        const content = $(`#${string}_${type}_content`)[0];
        $(`#${string}_${type}_btn`)[0].addEventListener('click', ()=>{
            view_div(string, type);
            if(div.style.display == 'block'){
                div.style.display = 'none';
            } else if(div.style.display == 'none'){
                error.style.display = 'none';
                content.style.display = 'none';
                const xhr = new XMLHttpRequest();
                xhr.open('POST', `./classes/inc/admin_${string}_render.inc.php`, false);
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
                        xhr.send(`sd=${$(`#${type}_startdate`)[0].value}&ed=${$(`#${type}_enddate`)[0].value}&t=${type}`);
                        break;
                    default:
                        xhr.send(`t=${type}`);
                }
                div.style.display = 'block';
            }
        });
    }
}
view_data('view', 'iqa');
view_data('logs', 'iqa');
view_data('view', 'course');
view_data('logs', 'course');
view_data('view', 'learner');
view_data('logs', 'learner');
//Function is used to retrieve log data dependant on the form data
['iqa', 'course', 'learner'].forEach(function(type){
    $(`#logs_${type}_filter_form`)[0].addEventListener('submit', (e)=>{
        e.preventDefault();
        const error = $(`#logs_${type}_error`)[0];
        const content = $(`#logs_${type}_content`)[0];
        content.style.display = 'none';
        error.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `./classes/inc/admin_logs_render.inc.php`, false);
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
        xhr.send(`sd=${$(`#${type}_startdate`)[0].value}&ed=${$(`#${type}_enddate`)[0].value}&t=${type}`);
    });
})
function header_clicked(string, integer, type){
    if((string == 'view' || string == 'logs') && (type == 'iqa' || type == 'course' || type == 'learner')){
        //set the headers of the table to contain the correct arrow showing how the table is sorted
        const headers = $(`#${string}_${type}_thead`).find('tr:first th');
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
        const body = $(`#${string}_${type}_tbody`).find('tr');
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
            const tbody = $(`#${string}_${type}_tbody`)[0];
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
function select_changed(type){
    if(type == 'assign' || type == 'remove'){
        $(`.${type}au2`).each(function(index, item){
            $(this)[0].hidden = true;
            $(this)[0].hidden = true;
        });
        $(`.${type}au2_${$(`#${type}_learner_au`)[0].value}`).each(function(index, item){
            $(this)[0].hidden = false;
            $(this)[0].disabled = false;
        });
        $(`#${type}_learner_au2`)[0].style.display = 'block';
        $(`#${type}_learner_au3`)[0].style.display = 'block';
    }
}