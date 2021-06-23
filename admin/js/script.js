var pag_index = 0;
var pages_count = 0;
var current_chat_id = 0;
var type = 0;

function getUserInfo(id, type) {
    var values = {
        "chat_id": id, "type": type
    };
    $.ajax({
        type: "POST", url: "actions/get_user_info.php", data: values, success: function (result) {
            if (result == 0) {
                $('#show-error').modal();
                return;
            }
            var table = document.getElementById('user-info-table').children[0];
            var json = JSON.parse(result);
            table.children[0].children[1].innerHTML = json['ID'];
            table.children[1].children[1].innerHTML = json['name'];
            table.children[2].children[1].innerHTML = '';//json['telegram_name'];
            table.children[3].children[1].innerHTML = json['register_date'];
            table.children[4].children[1].innerHTML = json['Balance'];
            table.children[5].children[1].innerHTML = json['habits_count'];
            current_chat_id = json['ID'];
            var table = document.getElementById('habits_table');
            var body = table.getElementsByTagName('tbody')[0];
            body.innerHTML = '';
            json = json['habits'];
            for (var i = 0; i < json.length; i++) {
                var row = body.insertRow(i);
                var cell0 = row.insertCell(0);
                var cell1 = row.insertCell(1);
                var cell2 = row.insertCell(2);
                var cell3 = row.insertCell(3);
                var cell4 = row.insertCell(4);
                var cell5 = row.insertCell(5);
                var cell6 = row.insertCell(6);
                var cell7 = row.insertCell(7);
                var cell8 = row.insertCell(8);
                cell0.innerHTML = i+1;
                cell1.innerHTML = json[i]['Name'];
                cell2.innerHTML = json[i]['Mistake_price'];
                cell3.innerHTML = json[i]['ReportDays'];
                cell4.innerHTML = json[i]['ReportTime'];
                cell5.innerHTML = json[i]['WarnHours'];
                cell6.innerHTML = json[i]['WeeksCount'];
                cell7.innerHTML = json[i]['StartDate'];
                cell8.innerHTML = '';
            }
            $('#show-user-info').modal();
        }, error: function (error) {
            return 0;
        }
    })
}

function changeGroupType(tp) {
    type = tp;
    pag_index = 0;
    document.getElementsByClassName('page-item')[1].childNodes[1].innerText = 1;
    getUsers();
}

function admitToNextStage(id, self) {
    var r = confirm("Перевести пользователя на следующий этап?");
    if (r) {
        var values = {
            "chat_id": id
        };
        $.ajax({
            type: "POST", url: "actions/admit_to_next_stage.php", data: values, success: function (result) {
                if (result == 1) {
                    alert("Пользователь переведен на следующий этап");
                    self.parentNode.removeChild(self);
                }
            }, error: function (error) {
            }
        });
    }
}

function getUsers() {
    $.ajax({
        type: 'POST',
        data: 'page=' + pag_index + '&type=' + type,
        url: 'actions/get_users.php',
        success: function (result) {
            var table = document.getElementById('users_table');
            var body = table.getElementsByTagName('tbody')[0];
            body.innerHTML = '';
            var json = JSON.parse(result);
            var users_count = json[1];
            pages_count = Math.ceil(users_count / 100);
            document.getElementById('page_count_span').innerText = pages_count;
            document.getElementById('users_total_count').innerText = json[1];
            document.getElementById('users_total_count_today').innerText = json[2];
            document.getElementById('users_total_count_yesterday').innerText = json[3];
            json = json[0];
            for (var i = 0; i < json.length; i++) {
                var row = body.insertRow(i);
                var cell0 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                cell0.innerHTML = json[i]['ID'];
                cell2.innerHTML = json[i]['name'];
                cell3.innerHTML = json[i]['Balance'];
                cell4.innerHTML = json[i]['register_date'];
                cell5.innerHTML = json[i]['habitsCount']==null?0:json[i]['habitsCount'];
                cell6.innerHTML = '<a style="cursor: pointer" onclick="getUserInfo(' + json[i]['ID'] + ',0)" class="fas fa-eye view-info"></a>';
                if (json[i]['has_access_next_stage'] == 0) {
                    cell6.innerHTML += '<a style="cursor: pointer" onclick="admitToNextStage(' + json[i]['ID'] + ',this)" class="far fa-check-circle mx-1"></a>';
                }
            }
        },
        error: function (error) {
        }
    });
}

function getFilteredUsersByStep(type) {
    $.ajax({
        type: 'POST',
        data: 'type=' + type,
        url: 'actions/get_users_by_step.php',
        success: function (result) {
            var table = document.getElementById('filtered_users_table');
            var body = table.getElementsByTagName('tbody')[0];
            body.innerHTML = '';
            var json = JSON.parse(result);
            var users_count = json[1];
            json = json[0];
            for (var i = 0; i < json.length; i++) {
                var row = body.insertRow(i);
                var cell0 = row.insertCell(0);
                var cell1 = row.insertCell(1);
                var cell2 = row.insertCell(2);
                var cell3 = row.insertCell(3);
                var cell4 = row.insertCell(4);
                var cell5 = row.insertCell(5);
                var cell6 = row.insertCell(6);
                var cell7 = row.insertCell(7);
                cell0.innerHTML = '<input type="checkbox" class="checkbox" checked></input>';//json[i]['id'];
                cell1.innerHTML = json[i]['telegram_id'];
                cell2.innerHTML = json[i]['telegram_username'];
                cell3.innerHTML = json[i]['phone'];
                cell4.innerHTML = json[i]['telegram_name'];
                cell5.innerHTML = json[i]['register_date'];
                cell6.innerHTML = json[i]['stage'] == 4 ? 'VIP' : json[i]['stage'];
                cell7.innerHTML = '<a style="cursor: pointer" onclick="getUserInfo(' + json[i]['telegram_id'] + ',0)" class="fas fa-eye view-info"></a>';
                if (json[i]['has_access_next_stage'] == 0) {
                    cell7.innerHTML += '<a style="cursor: pointer" onclick="admitToNextStage(' + json[i]['telegram_id'] + ',this)" class="far fa-check-circle mx-1"></a>';
                }
            }
        },
        error: function (error) {
        }
    });
}

String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

function save() {
    var values = {
        "Param": document.getElementById('jsonKeys').options[document.getElementById('jsonKeys').selectedIndex].getAttribute('value'),
        "Value": document.getElementById('jsoneditor').value,
    };
    $.ajax({
        type: "POST", url: "actions/save_json_parameter.php", data: values, success: function (result) {
            if (result == 1) {
                alert("Сохранено");
            } else {
                alert("Функия отключена разработчиком!!!");
            }
        }, error: function (error) {
        }
    })
}

function loadItemByKey() {
    var values = {
        "Param": document.getElementById('jsonKeys').options[document.getElementById('jsonKeys').selectedIndex].getAttribute('value')
    };
    $.ajax({
        type: "GET", url: "actions/get_json_parameter.php", data: values, success: function (result) {
            document.getElementById('jsoneditor').value = result.replaceAll("<br>", "\r\n")
        }, error: function (error) {
        }
    })
}

function deleteCurrentUser() {
    var r = confirm("Вы действительно хотите удались пользователя?");
    if (r) {
        var values = {
            "chat_id": current_chat_id
        };
        $.ajax({
            type: "POST", url: "actions/delete_user.php", data: values, success: function (result) {
                if (result == 1) {
                    alert("Пользователь удален");
                    $('#show-user-info').modal('hide');
                    getUsers();
                }
            }, error: function (error) {
            }
        });
    }
}

function moveToUsers(id) {
    getFilteredUsersByStep(id);
    $('#nav-filtered-users-tab').tab('show');
}

function getTotalStatistics() {

    $.ajax({
        type: "POST", url: "actions/get_statistics.php", success: function (result) {
            var table = document.getElementById('total_statistics').children[0];
            var json = JSON.parse(result);
            table.children[0].children[1].innerHTML = json['users_count'];
            table.children[1].children[1].innerHTML = json['first_completed_count'];
            table.children[2].children[1].innerHTML = json['second_completed_count'];
            table.children[3].children[1].innerHTML = json['third_completed_count'];
            table.children[4].children[1].innerHTML = json['first_average_complete_time'];
            table.children[5].children[1].innerHTML = json['second_average_complete_time'];
            table.children[6].children[1].innerHTML = json['third_average_complete_time'];
            var table = document.getElementById('action_types_table');
            var body = table.getElementsByTagName('tbody')[0];
            body.innerHTML = '';
            for (var i = 0; i < json['action_types'].length; i++) {
                var row = body.insertRow(i);
                var cell0 = row.insertCell(0);
                var cell1 = row.insertCell(1);
                var cell2 = row.insertCell(2);
                var cell3 = row.insertCell(3);
                cell0.innerHTML = i + 1;//json['action_types'][i]['last_action_type'];
                cell1.innerHTML = json['action_types'][i]['name'];
                cell2.innerHTML = json['action_types'][i]['count'];
                cell3.innerHTML = '<a style="cursor: pointer" onclick="moveToUsers(' + json['action_types'][i]['last_action_type'] + ',0)" class="fas fa-comment-dots"></a>';
            }
        }, error: function (error) {
        }
    });
}

function changePage(caller) {
    var pags = document.getElementsByClassName('page-item');
    if (pags[0] == caller.currentTarget) {
        if (pag_index != 0) {
            pag_index--;
            pags[1].childNodes[1].innerText = pag_index + 1;
            document.getElementById('current_index_span').innerHTML = pag_index + 1;
            getUsers();
        }
    } else if (pags[2] == caller.currentTarget) {
        if (pag_index + 1 < pages_count) {
            pag_index++;
            pags[1].childNodes[1].innerText = pag_index + 1;
            document.getElementById('current_index_span').innerHTML = pag_index + 1;
            getUsers();
        }
    }
}

function loadJsonKeys() {
    $.ajax({
        type: "GET", url: "actions/get_json_keys.php", success: function (result) {
            var json = JSON.parse(result);
            var select = document.getElementById("jsonKeys");
            for (var i = 0; i < json.length; i++) {
                select.options[i] = new Option(json[i], json[i]);
            }
            select.onchange();

        }, error: function (error) {
        }
    });
}

function sendMessageToSelectedUsers() {
    var text = document.getElementById('send_message_body').value;
    if (text.length == 0) {
        alert("Сообщение пустое");
        return;
    }
    var table = document.getElementById('filtered_users_table');
    var body = table.getElementsByTagName('tbody')[0];
    var ids = [];
    for (var i = 0; i < body.childNodes.length; i++) {
        if (body.childNodes[i].childNodes[0].childNodes[0].checked) {
            var id = body.childNodes[i].childNodes[1].innerText;
            ids.push(id);
        }
    }
    if (ids.length == 0) {
        alert("Не выбран ни один пользователь");
        return;
    }
    var values = {
        "message": text,
        "users": ids.join(',')
    };
    $.ajax({
        type: "POST", url: "actions/send_message_to_users.php", data: values, success: function (result) {
            if (result == 1) {
                var text = document.getElementById('send_message_body').value = '';
                $('#show-send-message').modal('hide');
                alert("Сообщение отправлено");
            }
        }, error: function (error) {
        }
    })
}

var checkedAll = true;
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('.page-item').on('click', function (caller) {
        changePage(caller);
    });
    $('#get-user-info-button').on('click', function () {
        var val = document.getElementById('username_input').value;
        var result = getUserInfo(val, 1);
    });
    $('#open-send-message-button').on('click', function () {
        $('#show-send-message').modal();
    });
    $('#send-message').on('click', function () {
        sendMessageToSelectedUsers();
    });
    $('#delete_user').on('click', function () {
        deleteCurrentUser();
    });
    $('.users_table_check').on('click', function () {
        var table = document.getElementById('filtered_users_table');
        var body = table.getElementsByTagName('tbody')[0];
        for (var i = 0; i < body.childNodes.length; i++) {
            body.childNodes[i].childNodes[0].childNodes[0].checked = !checkedAll;
        }
        checkedAll = !checkedAll;
    });
    if (document.getElementById("jsonKeys") != null)
        loadJsonKeys();
    if (document.getElementById('total_statistics') != null) {
        getTotalStatistics();
    }
    if (document.getElementById('users_table') != null)
        getUsers();
});