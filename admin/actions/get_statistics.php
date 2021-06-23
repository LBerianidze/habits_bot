<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 19.05.2020
 * Time: 11:55
 */
include 'check_auth.php';
include 'DBConfig.php';
$db_config = new DBConfig();
$result = array();
$result['users_count'] = $db_config->getUsersCount();
$result['first_completed_count'] = $db_config->getStageCompleteCount('first_done_date');
$result['second_completed_count'] = $db_config->getStageCompleteCount('second_done_date');
$result['third_completed_count'] = $db_config->getStageCompleteCount('third_done_date');
$first = $db_config->getFirstStageCompleteStatistic();
$second = $db_config->getSecondStageCompleteStatistic();
$third = $db_config->getThirdStageCompleteStatistic();
$result['first_average_complete_time'] = secondsToTime($first->sum / $first->count);
$result['second_average_complete_time'] = secondsToTime($second->sum / $second->count);
$result['third_average_complete_time'] = secondsToTime($third->sum / $third->count);
$actions_sequence = array(9,2,1,10,11,20,12,3,4,13,14,21,15,5,6,16,17,22,18,7,8,19);
$action_types = $db_config->getActionTypesStatistic();

$actions = array();
foreach ($actions_sequence as $item)
{
    $action = array();
    $action['last_action_type'] = $item;
    $action['name'] =  getActionTypeByID($item);
    foreach ($action_types as $action_type)
    {
        if($item == $action_type->last_action_type)
        {
            $action['count'] = $action_type->count;
            break;
        }
        else
        {
            $action['count'] = 0;
        }
    }
    $actions[] = $action;
}
foreach ($action_types as $action_type)
{
    $action_type->name = getActionTypeByID($action_type->last_action_type);
}

$result['action_types'] = $actions;
echo json_encode($result);

function secondsToTime($seconds)
{
    $t = round($seconds);
    return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
}

function getActionTypeByID($id)
{
    switch ($id)
    {
        case 1:
            return 'Подтвердил телефон';
        case 2:
            return 'Выдано сообщени о вводе неверного телефона';
        case 3:
            return 'Выдано меню после отправки идеи первого этапа';
        case 4:
            return 'Выдано меню после отправки инсайта первого этапа';
        case 5:
            return 'Выдано меню после отправки идеи второго этапа';
        case 6:
            return 'Выдано меню после отправки инсайта второго этапа';
        case 7:
            return 'Выдано меню после отправки идеи этап третьего этапа';
        case 8:
            return 'Выдано меню после отправки инсайта третьего';
        case 9:
            return 'Только что вошел в бота,спросили телефон';
        case 10:
            return 'Принял условия,отправили видео первого этапа';
        case 11:
            return 'Начал первый этап';
        case 12:
            return 'Перешел к описанию ДЗ или инсайта первого этапа';
        case 13:
            return 'Перешел к видео второго этапа';
        case 14:
            return 'Начал второй этап';
        case 15:
            return 'Перешел к описанию ДЗ или инсайта второго этапа';
        case 16:
            return 'Перешел к видео третьего этапа';
        case 17:
            return 'Начал третий этап';
        case 18:
            return 'Перешел к описанию ДЗ или инсайта третьего этапа';
        case 19:
            return 'Перешел к задачам для экспертов';
        case 20:
            return 'Перевели во второй этап';
        case 21:
            return 'Перевели в третий этап';
        case 22:
            return 'Закрыли третий этап';
    }
}