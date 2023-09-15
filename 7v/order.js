const panelTemplate=`
<div class="site-settings">
    <div class="site-settings__wr container">
        <div class="site-settings__form">
            <div class="site-settings__item">
                <label class="site-settings__label" for="site-settings__template">Шаблон главной</label>
                <select name="template" id="site-settings__template" class="site-settings__select">
                    <option value="1">Вариант 1</option>
                    <option value="2">Вариант 2</option>
                    <option value="3">Вариант 3</option>
                </select>
            </div>
            <div class="site-settings__item">
                <label class="site-settings__label" for="site-settings__theme">Цветовая схема</label>
                <select name="theme" id="site-settings__theme" class="site-settings__select">
                    <option value="sea">Море (blue-green)</option>
                    <option value="dark_matter">Графит (grey-green)</option>
                    <option value="pudding">Пудинг (red-maroon)</option>
                    <option value="pinky_girly">Конфетка (pinky) </option>
                    <option value="broccoli">Брокколи (green-yellow)</option>
                    <option value="dream">Мечта (blue-yellow)</option>
                    <option value="toad">Волна (green-greensea)</option>
                    <option value="algae">Водоросли (black-green)</option>
                    <option value="tuxedo">Смокинг (grey-blue)</option>
                    <option value="cocktail" selected="selected">Коктейль (blue-red)</option>
                </select>
            </div>

            <div class="site-settings__item">
                <label class="site-settings__label">Логотип</label>
                <button class="site-settings__button" id="js-site-settings__logos-show">Выбрать</button>
            </div>
            <div class="site-settings__item">
                <button class="site-settings__button site-settings__button_big js-site-settings__popup-order">Заказать</button>
            </div>
        </div>
    </div>
</div>


<div class="site-popup">
    <div class="site-logos">
        <div class="site-logo__list">

        </div>
    </div>
</div>


<div class="site-popup">
    <div class="site-order">
        <form action="/7v/form/index.php" class="site-order__form js-form-submit">
            <input type="hidden" name="form"  value="siteorder">
            <div class="site-order__row">
                <label for="site-order__os-name" class="site-order__label">ФИО</label>
                <input type="text" name="name" class="dn" value="">
                <input type="text" name="os-name" class="site-order__input" id="site-order__os-name" value="" required>
            </div>
            <div class="site-order__row">
                <label for="site-order__agency" class="site-order__label">Агентство</label>
                <input type="text" name="os-agency" class="site-order__input" id="site-order__os-agency" value="">
            </div>

            <div class="site-order__row">
                <label for="site-order__os-phone" class="site-order__label">Телефон</label>
                <input type="text" name="phone" class="dn form__protect" value="">
                <input type="text" name="os-phone" class="site-order__input" id="site-order__os-phone" value="" required>
            </div>
            <div class="site-order__row">
                <label for="site-order__os-email" class="site-order__label">Email</label>
                <input type="text" name="os-email" class="site-order__input" id="site-order__os-email" value="">
            </div>
            <div class="site-order__row">
                <label for="site-order__os-comment" class="site-order__label">Комментарий</label>
                <input type="text" name="os-comment" class="site-order__input" id="site-order__os-comment" value="">
            </div>

            <div class="site-order__row">
                <label class="site-order__label">Логотип</label>
                <input type="hidden" name="os-logosrc" class="site-order__input" id="site-order__os-logosrc" value="">
                <div class="site-order__logo">
                    <img class="js-site-order__logo-img" src="/7v/logo/1.png">
                </div>
            </div>
            <div class="site-order__row">
                <label for="site-order__os-colorscheme" class="site-order__label">Цветовая схема</label>
                <input type="text" name="os-colorscheme" class="site-order__input" id="site-order__os-colorscheme" value="" disabled>
            </div>
            <div class="site-order__row">
                <label for="site-order__os-hometpl" class="site-order__label">Шаблон главной</label>
                <input type="text" name="os-hometpl" class="site-order__input" id="site-order__os-hometpl" value="" disabled>
            </div>
            <div class="site-order__row personal-field">
					<input name="personal" type="checkbox" required="true" value="1" id="quick-order__pers-call"><label for="quick-order__pers-call">Подтверждаю согласие на <a href="/politika" target="_blank">обработку персональных данных</a></label>
			</div>
            <div class="site-order__row">
                <label class="site-order__label">&nbsp;</label>
                <input type="submit" class="site-order__submit" value="Заказать">
            </div>
        </form>
        <div class="site-order__error form__error">Возникла ошибка!</div>
        <div class="site-order__success form__success">Заявка отправлена!<br>Мы свяжемся с Вами.</div>
    </div>
</div>

<style>

.site-popup{
    display: none;
}

.site-settings {
    font-size: 14px;position: relative;background: #f0f0f0;box-shadow: 0 2px 3px 0 rgba(30, 30, 30, .5);
    padding: 8px 0;
}
.site-settings__wr {
    width: 1170px;
    margin: 0 auto;
}
.site-settings__form {
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    align-items: center;
}
.site-settings__item {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
}
.site-settings__item+.site-settings__item{
    margin-left: 2%;
}
.site-settings__item:last-child {
    flex: 0 0 auto;
}
.site-settings__label {
    margin-right: 12px;
    white-space: nowrap;
}
.site-settings__select {
    height: 26px;
}
.site-settings__button {
    padding: 6px 1em;
    cursor: pointer;
    color: #fff;
    border: none;
    border-radius: 3px;
    background: #aaa;
}
.site-settings__button_big {
    background: #da5151;
    text-transform: uppercase;
}
.site-settings__button:hover {
    opacity: 0.85;;
}

.site-popup { }
.site-logos {
    width: 80%;
    max-width: 1400px;
}
.site-logo__list {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;

}
.site-logo__item {
    flex: 0 1 100px;
    padding: 10px;
}
.site-logo__item:hover{
    outline: solid 3px #fffce1;
}
.site-logo__img {
    max-height: 100px;
    cursor: pointer;
}
.site-order {
    width: 90%;
    max-width: 400px;
}
.site-order__form { }
.site-order__row {
    margin-bottom: 10px;
}
.site-order__label {
    display: block;
    margin-bottom: 5px;
}
.site-order__input {
    height:28px;
    width: 100%;
    box-sizing: border-box;
}
.site-order__submit {
    padding: 6px 1em;
    cursor: pointer;
    color: #fff;
    border: none;
    border-radius: 3px;
    background: #da5151;
    text-transform: uppercase;
    font-size: 1.2em;
}
.site-order__submit:hover {
    opacity:0.85;
}
.site-order__error,
.site-order__success{
    text-align:center;
    font-size:18px;
    display:none;
}
.site-order__error {
    color:red;
}
.site-order__success {
    color:green;
}
.site-order__logo{
    text-align:center
}

@media (max-width:767px){
	.site-settings{
		display:none
	}
}
</style>
`;

$('body').prepend(panelTemplate);


siteSetting =(function() {
    const   TEMPLATEPATH=['/', '/home2', '/home3'],
            THEMENAME=[
                'sea',
                'dark_matter',
                'pudding',
                'pinky_girly',
                'broccoli',
                'dream',
                'toad',
                'algae',
                'algae',
                'tuxedo',
                'cocktail'
                ],
            LOGOMAXID=74,
            LOGOPATH='/7v/logo/',

            SETTINGS={
                template: 0,
                theme: 0,
                logo:1

            },
            $LOGO= $('.logo__img');

    let setTemplate = function (id){
        SETTINGS.template=id;
        localStorage.setItem('setings', JSON.stringify(SETTINGS));
       // document.location= document.location.origin+TEMPLATEPATH[id];
    }

    let setTheme = function (id) {
        SETTINGS.theme = id;
        localStorage.setItem('setings', JSON.stringify(SETTINGS));
        $('html').attr('class', `theme-${SETTINGS.theme}`);
    }

    let setLogo = function (id) {
        SETTINGS.logo = id;
        localStorage.setItem('setings', JSON.stringify(SETTINGS));
    }

    let setSettings=function(){
        if(localStorage.getItem('setings')!=null){
            let settings= JSON.parse(localStorage.getItem('setings'));
            SETTINGS.logo= settings.logo;
            SETTINGS.theme = settings.theme;
            SETTINGS.template = settings.template;

            $('#site-settings__template option').eq(SETTINGS.template-1).attr('selected', 'selected');
            $(`#site-settings__theme option[value=${SETTINGS.theme}]`).attr('selected', 'selected');
            $LOGO.attr('src', LOGOPATH + SETTINGS.logo + '.png');

            $('html').attr('class', `theme-${SETTINGS.theme}`);
        }
    }
    let renderLogos = function(){
        for (let index = 1; index <= LOGOMAXID; index++) {
            $('.site-logo__list').append('<div class="site-logo__item"><img src="'+ LOGOPATH+index+'.png" class="site-logo__img"></div>');
            
        }
    }

    let init= function(){
        setSettings();
        renderLogos();

        let templateControl= document.getElementById('site-settings__template');
        templateControl.addEventListener('change', function(){
            setTemplate(templateControl.options[templateControl.selectedIndex].value);
            document.location = TEMPLATEPATH[templateControl.options[templateControl.selectedIndex].value-1];
        });

        let themeControl = document.getElementById('site-settings__theme');
        themeControl.addEventListener('change', function () {
            setTheme(themeControl.options[themeControl.selectedIndex].value)
        });        

        let logoControl = document.getElementById('js-site-settings__logos-show');
        logoControl.addEventListener('click', function () {
            $.fancybox.open($('.site-logos'));
        });

        $('.site-logo__list').on('click','.site-logo__img', function(){
            setLogo($('.site-logo__img').index($(this))+1);
            $.fancybox.close();
            $LOGO.attr('src', LOGOPATH + SETTINGS.logo + '.png');

        });

        $('.js-site-settings__popup-order').on('click', function(){
            $('#site-order__os-logosrc').val(LOGOPATH+SETTINGS.logo+'.png');
            $('.js-site-order__logo-img').attr('src', LOGOPATH + SETTINGS.logo + '.png');
            $('#site-order__os-colorscheme').val(themeControl.options[themeControl.selectedIndex].text);
            $('#site-order__os-hometpl').val('Вариант '+SETTINGS.template);
            $.fancybox.open($('.site-order'));
        })

    }

    init();

    return {
        setTemplate: setTemplate,
        setTheme: setTheme
    }
})();

    // siteSetting1=new siteSetting();
