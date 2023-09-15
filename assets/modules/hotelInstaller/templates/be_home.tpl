<link rel="stylesheet" type="text/css" href="/assets/modules/hotelInstaller/css/framework.css">
<!-- <link rel="stylesheet" type="text/css" href="media/style/default/css/styles.min.css"> -->
<!-- <link rel="stylesheet" type="text/css" href="/assets/modules/hotelInstaller/css/admin.css"> -->

<style>
    .darkness ::placeholder {
        color: #eee;
    }

    [type="submit"] {
        margin: 20px 0;
    }

    #bookingUrls {
        width: 100%;
        min-height: 100px;
    }

    #msg {
        margin: 10px 0;
    }

    #msg.error,
    #msg.danger {
        border-left: 4px solid #dc3545;
    }

    .progress {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        height: 1rem;
        overflow: hidden;
        font-size: .75rem;
        background-color: #e9ecef;
        border-radius: .25rem;
    }

    .progress-bar {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        background-color: #007bff;
        transition: width .6s ease;
        white-space: nowrap;
        padding: 0 5px;
    }

    .progress-bar:empty {
        padding: 0;
    }

    .progress-bar[aria-valuenow="100"] {
        background-color: #28a745;
    }

    .progress-bar {
        background-color: #ffc107;
    }

    .progress-bar.danger {
        background-color: #dc3545;
    }

    #urlListArray>div {
        padding: 5px 0 5px 20px;
        border-top: 1px solid #c3c3c3;
    }

    #urlListArray>div:first-child {
        border-top: none;
    }

    .hotelServer {
        font-size: 12px;
        line-height: 14px;
    }

    .hotelServer span {
        background: #c3c3c3;
        color: #fff;
        border-radius: 5px;
        padding: 1px 5px 1px;
        font-size: 12px;
        line-height: 12px;
        position: relative;
        top: -1px;
    }

    .hotelProcessed {
        margin-top: 5px;
        position: relative;
    }

    .hotelProcessed:before {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 10px;
        position: absolute;
        top: 50%;
        left: -15px;
        margin-top: -4px;
        background-color: #c3c3c3;
    }

    /* .hotelServer span */
    .hotelProcessed.success:before {
        background-color: #28a745;
    }

    .hotelProcessed.error:before {
        background-color: #dc3545;
    }

    .hotelProcessed.warning:before {
        background-color: #ffc107;
    }

    .hotelProcessed.info:before {
        background-color: #17a2b8;
    }

    .hotelProcessed.primary:before {
        background-color: #007bff;
    }

    #urlListArray .controls {
        float: right;
        margin-left: 10px;
    }

    #hotelProgressError {
        display: none;
    }

    #send {
        padding: 5px 10px;
        background-color: #28a745;
        color: #fff;
    }

    #urlCid {
        width: 180px;
    }
</style>
</head>

<body>
    <form id='addHotels' class='module p15'>
        <h1>Инсталлятор отелей</h1>
        <input type="hidden" name="action" value='addHotels'>
        <input type="hidden" name="start_operation" value='1'>
        <input type="hidden" name="pending_time" value='10'>
        <!--
    <div class="field">
        <div class="label">URL страницы отеля с сайта <a href="https://www.booking.com/" target="_blank">booking.com</a></div>
            <input type="text" name="url" placeholder='URL страницы' value=''>
        <div>
            <small>
            <a href="https://www.booking.com/hotel/gb/hilton-london-docklands.ru.html">
                https://www.booking.com/hotel/gb/hilton-london-docklands.ru.html
            </a>
            </small>
        </div>
    </div>
    -->
        <div class="field">
            <div class="label">Множественная загрузка <small>(построчно впишите список URL)</small></div>
            <textarea name="urls" id='bookingUrls' placeholder="https://www.booking.com/hotel/gb/shangri-la-at-the-shard-london.ru.html">
            https://www.booking.com/hotel/gb/shangri-la-at-the-shard-london.ru.html
            </textarea>
            <!-- https://www.booking.com/hotel/gb/shangri-la-at-the-shard-london.ru.html -->
            <!-- https://www.booking.com/hotel/xc/yalta-intourist.ru.html -->
            <!-- https://www.booking.com/hotel/ru/otiel-quot-aqua-villa-quot.ru.html -->
        </div>
        <div class="field hidden">
            <div class="label">ID ресурса родителя для загрузки отелей</div>
            <input type="number" name="parent_id" id="urlCid" placeholder='' value=''>
        </div>
        <div class="field">
            <div class="label">Структура для сохранения отелей</div>
            <label>
                <input type="radio" name="upload_type" value="save_to_hotels_country_city" checked>
                Отели / Страна / Город
            </label>
            <label>
                <input type="radio" name="upload_type" value="save_to_hotels_city">
                Отели / Город
            </label>
        </div>

        <div class="field">
            <label>
                <input type="checkbox" name="create_parent_structure" value="1" checked>
                Создавать отсутствующую структуру родителей
            </label>
        </div>

        <!--<div class="field">
        <label>
            <input type="checkbox" name="save_to_cities" value="1">
            загружать в стране в: Отели / Город
        </label>
    </div>
    <div class="field">
        <label>
            <input type="checkbox" name="save_to_geocities" value="1">
            загружать в стране в: Города / Город / Отели
        </label>
    </div>
    <div class="field">
        <label>
            <input type="checkbox" name="save_to_city_name" value="1">
            искать совпадение города
        </label>
    </div> -->

        <div class="field">
            <input type="submit" value='Отправить запрос' id="send">
        </div>
    </form>

    <div class="p15">
        <div>Отелей на сервере</div>
        <div class="progress" id="progressServer">
            <div class="progress-bar" role="progressbar" style="flex-basis: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <div>Отелей сохранено</div>
        <div class="progress" id="progressSave">
            <div class="progress-bar" role="progressbar" style="flex-basis: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <div id="hotelProgressError">
            <div>Отели с ошибкой</div>
            <div class="progress" id="progressError">
                <div class="progress-bar danger" role="progressbar" style="flex-basis: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>

        <div id="msg" class='p10'></div>

        <div id="urlListArray" class='p10'></div>
    </div>

    <script>
        top.tree.openNode = function(node, indent, parent, expandAll, privatenode) {
            top.tree.privatenode = (!top.tree.privatenode || top.tree.privatenode == '0') ? top.tree.privatenode = '0' : top.tree.privatenode = '1';
            top.tree.rpcNode = $(node.parentNode.lastChild);

            var rpcNodeText;
            var loadText = "Загружается дерево сайта...";

            var signImg = top.tree.document.getElementById("s" + parent);
            var folderImg = top.tree.document.getElementById("f" + parent);

            // expand
            if (signImg && signImg.src.indexOf('media/style/MODxRE/images/tree/plusnode.gif') > -1) {
                signImg.src = 'media/style/MODxRE/images/tree/minusnode.gif';
                folderImg.src = (privatenode == '0') ? 'media/style/MODxRE/images/tree/application_double.png' : 'media/style/MODxRE/images/tree/application_double_key.png';
            }

            rpcNodeText = top.tree.rpcNode.innerHTML;

            if (rpcNodeText == "" || rpcNodeText.indexOf(loadText) > 0) {
                var i, spacer = '';
                for (i = 0; i <= indent + 1; i++) spacer += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                top.tree.rpcNode.style.display = 'block';
                //Jeroen set opened
                top.tree.openedArray[parent] = 1;
                //Raymond:added getFolderState()
                var folderState = top.tree.getFolderState();
                top.tree.rpcNode.innerHTML = "<span class='emptyNode' style='white-space:nowrap;'>" + spacer + "&nbsp;&nbsp;&nbsp;" + loadText + "...<\/span>";
                new Ajax('index.php?a=1&f=nodes&indent=' + indent + '&parent=' + parent + '&expandAll=' + expandAll + folderState, {
                    method: 'get',
                    onComplete: top.tree.rpcLoadData
                }).request();
            } else {
                top.tree.rpcNode.style.display = 'block';
                //Jeroen set opened
                top.tree.openedArray[parent] = 1;
            }
        }

        var timer;
        var timeInit = false;
        var urls;
        var queryAnswerReceived = true;

        jQuery('#addHotels').submit(function(e) {
            e.preventDefault();
            jQuery('#msg').removeClass('error').data('queries', 0);
            jQuery('#hotelProgressError').hide();
            jQuery('#urlListArray').html('');

            jQuery('#progressServer').find('.progress-bar')
                .attr('style', 'flex-basis: ' + 0 + '%')
                .attr('aria-valuenow', 0)
                .html('');
            jQuery('#progressSave').find('.progress-bar')
                .attr('style', 'flex-basis: ' + 0 + '%')
                .attr('aria-valuenow', 0)
                .html('');

            if (!jQuery('#addHotels').hasClass('wait')) {
                jQuery('#addHotels').addClass('wait');
                urls = jQuery('#bookingUrls').val();
                jQuery('#msg').html('Запрос отправляется, не закрывайте страницу');
                initQuery();
            } else {
                jQuery('#msg').html('Запрос выполняется, ожидайте завершения');
            };
        });

        var pendingTime = jQuery('[name="pending_time"]').val() * 1000;

        function initQuery() {
            console.log('timer:');
            console.log(timer);
            console.log('start_operation ' + jQuery('[name="start_operation"]').val());
            if (!queryAnswerReceived) { // не получен ответ - запрос пропускаем
                console.log('CANCEL ajax, queryAnswerReceived is false');
                return false;
            }

            jQuery.ajax({
                type: 'post',
                dataType: 'json',
                data: jQuery('#addHotels').serialize(),
                beforeSend: function() {
                    queryAnswerReceived = false;
                },
                complete: function(response) {
                    console.log('complete');
                    console.log(response);
                    if (response != 'success' && typeof(response.responseJSON) == 'undefined') {
                        alert('Получен некорректный ответ');
                        reEneableForm();
                        return false;
                    }
                },
                success: function(response) {
                    console.log('success');
                    console.log(response);
                    if (response != 'success' && typeof(response.status) == 'undefined') {
                        alert('Получен некорректный ответ');
                        reEneableForm();
                        return false;
                    }

                    jQuery('#msg').data('queries', (jQuery('#msg').data('queries') + 1));
                    jQuery('#progressServer').find('.progress-bar').removeClass('warning');
                    queryAnswerReceived = true;

                    if (response.status == 'error') {
                        console.log('ERROR');
                        reEneableForm();
                        jQuery('#msg').addClass('error').html(response.msg);
                        return false;
                    }

                    if ((!valide(response) && !timeInit) || response.status == 'waiting') {
                        if (response.status == 'waiting')
                            jQuery('#progressServer').find('.progress-bar').addClass('warning');

                        if (response.status == 'limit') {
                            console.log('LIMIT');
                            reEneableForm();
                            jQuery('#msg').addClass('error').html(response.msg);
                        } else {
                            console.log('AGAIN ajax, checking timer');
                            timeInit = true;
                            if (typeof(timer) == 'undefined') {
                                jQuery('#msg').html('Запрос обработан, ожидайте завершения в ближайшее время. Не закрывайте страницу');
                                console.log('Set timer');
                                timer = setInterval(function() {
                                    jQuery('[name="start_operation"]').val(0);
                                    console.log('!!! initQuery !!!');
                                    initQuery();
                                }, pendingTime);
                            }
                            // jQuery('#msg').html('Запрос обработан, ожидайте завершения в ближайшее время. Не закрывайте страницу');
                        }
                        top.tree.updateTree();
                        return true;
                    } else if (valide(response) && timeInit) {
                        console.log('Done');
                        reEneableForm();
                        jQuery('#msg').html('Обработка закончена, результаты работы ниже');
                        timeInit = false; // ? - тестово вставил
                        top.tree.updateTree();
                        return true;
                    };
                    jQuery('#addHotels').removeClass('wait');
                    return true;
                },
            });
        };

        function reEneableForm() {
            jQuery('[name="start_operation"]').val(1);
            jQuery('#addHotels').removeClass('wait');
            if (typeof(timer) != 'undefined')
                clearInterval(timer);
            queryAnswerReceived = true;
        }

        function valide(result) {
            var results = false;
            var html = '';
            if (typeof(result.data.links) == 'undefined')
                return false;
            for (var i = 0; i < result.data.links.length; i++) {
                if (result.data.links[i].status == 3)
                    var badgeClass = "success";
                else if (result.data.links[i].status == 2)
                    var badgeClass = "warning";
                else if (result.data.links[i].status == 1)
                    var badgeClass = "primary";
                else if (result.data.links[i].status == 0)
                    var badgeClass = "danger";
                else
                    var badgeClass = "info";

                html += `
        <div data-id="${result.data.links[i].id}">
            <div class="hotelServer">
                <span>${i+1}</span> ${result.data.links[i].name}
            </div>
            <div class="hotelServer">
                <a href="${result.data.links[i].url}" target="_blank" class="badge badge-${badgeClass}">${result.data.links[i].url}</a>
            </div>
            <div class="hotelProcessed ${result.data.links[i].processing.status}">
                ${result.data.links[i].processing.msg}
                ${result.data.links[i].processing.warning}
            </div>
        </div>`;
            };
            jQuery('#progressServer').find('.progress-bar')
                .attr('style', 'flex-basis: ' + result.data.progress + '%')
                .attr('aria-valuenow', result.data.progress)
                .html(result.data.done + ' / ' + result.data.total);
            jQuery('#progressSave').find('.progress-bar')
                .attr('style', 'flex-basis: ' + result.processed.progress + '%')
                .attr('aria-valuenow', result.processed.progress)
                .html(result.processed.done + ' / ' + result.data.total);
            if (result.processedError.progress > 0) {
                jQuery('#progressError').find('.progress-bar')
                    .attr('style', 'flex-basis: ' + result.processedError.progress + '%')
                    .attr('aria-valuenow', result.processedError.progress)
                    .html(result.processedError.done + ' / ' + result.data.total);
                jQuery('#hotelProgressError').show();
            }
            if (result.data.progress == 100) {
                jQuery('#msg').html('Данные на сервере об отелях получены');
                // results = true;
            } else {
                jQuery('#msg').html('Ожидайте данных с сервера. Запросов на сервер выполнено: ' + jQuery('#msg').data('queries'));
            };
            if (result.processed.progress == 100 || result.data.total == (result.processedError.done + result.processed.done)) {
                jQuery('#msg').html('Цикл работы с админкой окончен.');
                jQuery('[name="start_operation"]').val(1);
                top.tree.updateTree();
                results = true;
            }
            jQuery(document).find('#urlListArray').html(html);
            return results;
        };
    </script>