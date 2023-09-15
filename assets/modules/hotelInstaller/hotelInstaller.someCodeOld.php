// foreach ($answer->data as $queryResultData) {
// } else if (
// property_exists($answer, 'status')
// and ((int) $answer->status === 0)
// and ($queryResultData->message === 'Url added to waiting list' || $queryResultData->message === 'Url exists in waiting list')
// ) {
// $processing['msg'] = 'В очереди на обработку данных. Обработка занимает 2-3 минуты';
// $processing['status'] = 'warning';
// $result['status'] = 'waiting';
// $result['data']['links'][] = array(
// 'url' => $queryResultData->data->url,
// 'name' => "Нет данных об отеле на сервере",
// 'status' => 2,
// 'processing' => $processing
// );
// $this->log .= "Отель обрабатывается на сервере: {$queryResultData->data->url}" . "\n" . (time() - $ltime) . "\n";
// } else if (
// property_exists($answer, 'status')
// and ((int) $answer->status === 0)
// and $queryResultData->message === 'Url is not added'
// ) {
// $processing['msg'] = "URL не может быть обработан на сервере: {$queryResultData->data->url}";
// $processing['status'] = 'error';
// $result['status'] = 'waiting';
// $result['data']['links'][] = array(
// 'url' => $queryResultData->data->url,
// 'name' => "URL не может быть обработан на сервере",
// 'status' => 3,
// 'processing' => $processing
// );
// $this->log .= "URL не может быть обработан на сервере: {$queryResultData->data->url}" . "\n" . (time() - $ltime) . "\n";
// }