(function(){
    var CountriesCalendar = function(opts){
        this.options = Object.assign(CountriesCalendar.defaults, opts);
        this.elId = document.getElementById(opts.elId);
        this.form = this.elId.previousElementSibling;
        this.moreButton = this.elId.nextElementSibling;
        initializeEvents(this);
        this.update();
        
    }

    CountriesCalendar.prototype.update = function(){
        this.showedCountriesIndex = 0;
        this.moreButton.classList.add("d-none");
        updateSettings(this);
        showResults(this);
        if (this.filteredCountries.length > this.options.display)
            this.moreButton.classList.remove("d-none");
        return this;
    };

    CountriesCalendar.prototype.showMore = function(){
        showResults(this, true);
        if (this.showedCountriesIndex >= this.filteredCountries.length)
            this.moreButton.classList.add("d-none");
        return this;
    };

    function initializeEvents(CC) {
        CC.form.addEventListener('change', CC.update.bind(CC));
        CC.moreButton.addEventListener('click', CC.showMore.bind(CC));
    }

    function updateSettings(CC) {        
        CC.activeMonth = parseInt(getRadioCheckedValue(CC,'month'));
        setCookie('CountriesCalendar_month',CC.activeMonth,7);
        CC.visa = !CC.form.elements['visa'].checked;
        CC.tourTypes = [];
        for (i=0;i<CC.form.elements['type'].length;i++)
            if (CC.form.elements['type'][i].checked)
                CC.tourTypes.push(CC.form.elements['type'][i].value);
        
        setCookie('CountriesCalendar_tourTypes',CC.tourTypes,7);
        filterCountries(CC);
    }

    function filterCountries(CC) {
        CC.filteredCountries = [];

        for (i=0;i<CC.options.data.length;i++)
        {
            if (CC.visa == false && CC.options.data[i].visa != 0) {
                continue;
            }

            var check = false;
            if (CC.tourTypes.length > 0)
            {
                
                for (tti=0;tti<CC.options.data[i].tourTypes.length;tti++) {
					if (typeof CC.options.data[i].tourTypesMonths[tti] === 'undefined')
						continue;
					
                    var index = inArray(CC.options.data[i].tourTypes[tti], CC.tourTypes);
                    if (index !== false)
                    {
                        if (inArray((CC.activeMonth+1),CC.options.data[i].tourTypesMonths[tti]) !== false)
                        {
                            check = true;
                            break;
                        }
                    }
                }
            }
            else
            {
                for (tti=0;tti<CC.options.data[i].tourTypes.length;tti++) {
					if (typeof CC.options.data[i].tourTypesMonths[tti] === 'undefined')
						continue;
					
                    if (inArray((CC.activeMonth+1),CC.options.data[i].tourTypesMonths[tti]) !== false)
                    {
                        check = true;
                        break;
                    }
                }
            }
            if (!check)
                    continue;
            CC.filteredCountries.push(CC.options.data[i].id);
        }
    }

    function showResults(CC, more=false){
        var html = '',
            filteredIndex = 0,
            initIndex = CC.showedCountriesIndex;

        if (CC.filteredCountries.length > 0)
        {
            for (i=0; i<CC.options.data.length; i++) {
                if (inArray(CC.options.data[i].id, CC.filteredCountries))
                {
                    filteredIndex++;
                    if ((initIndex) >= filteredIndex)
                        continue;
    
                    html += buildCard(CC.options.data[i], CC.activeMonth);
                    CC.showedCountriesIndex++;                
    
                    if (CC.showedCountriesIndex >= (initIndex+CC.options.display))
                        break;
                }
            }
        }
        else
        {
            html = '<div class="relax__noresults">Не найдены предложения</div>';
        }

        if (more)
            CC.elId.innerHTML += html;
        else
            CC.elId.innerHTML = html;

        showLazyImgs(CC);
    }

    function buildCard(cData,month){
        var html = '';
        if (cData.image == '')
            var image = ``;
        else
            var image = `<img data-src="${cData.image}" alt="" class="relax__img">`;
        
        if (cData.price_from > 0)
            var price_from = `<span class="relax__price">От ${cData.price_from} руб.</span>`;
        else
            var price_from = ``;

        var tourTypes = '';
        if (cData.tourTypes.length > 0) 
        {
            var tourTypes = `<span class="relax__type-icons">`;
            for (tti=0;tti<cData.tourTypes.length;tti++)
            {
                tourTypes += `<i class="relax__type-icon relax__type-icon_${cData.tourTypes[tti]}"></i>`;
            }
            tourTypes += `</span>`;
        } 
        else {
            var tourTypes = '';
        }
        
        if (cData.visa == 0)
            var visa = `<span class="relax__novisa">без виз</span>`;
        else
            var visa = ``;
        
        var temp = ``;
        if (cData['airTemp'][month] !== null)
            temp += `<span class="relax__temp"><i class="relax__icon relax__icon_air"></i><span class="relax__temp-value">${cData['airTemp'][month]} °C</span></span>`;
        if (cData['waterTemp'][month] !== null)
            temp += `<span class="relax__temp"><i class="relax__icon relax__icon_water"></i><span class="relax__temp-value">${cData['waterTemp'][month]} °C</span></span>`;
    
        html = `
        <div class="relax__country col-12 col-sm-6 col-md-6 col-lg-4">
            <a href="${cData.url}" class="relax__link">
                <span class="relax__figure">
                    ${image}
                    <span class="relax__country-name">${cData.pagetitle}</span>
                    ${price_from}
                    ${tourTypes}
                </span>
                <span class="relax__info">
                    ${temp}
                    ${visa}
                </span>
            </a>
        </div>
        `;

        return html;
    }

    function showLazyImgs(CC) {
        var imgs = CC.elId.getElementsByClassName('relax__img');
        for (i=0;i<imgs.length;i++)
        {
            if (imgs[i].hasAttribute('data-src'))
            {
                imgs[i].setAttribute('src', imgs[i].getAttribute('data-src'));
                imgs[i].onload = function() {
                    this.removeAttribute('data-src');
                };
            }
        }
    }

    function getActiveMonth(CC) {
        var activeMonthVal = getRadioCheckedValue(CC,'month');
        return CC.options.monthsEngShort[activeMonthVal];
    }

    function getRadioCheckedValue(CC, name)
    {
        var oRadio = CC.form.elements[name];
        for(var i = 0; i < oRadio.length; i++)
        {
            if(oRadio[i].checked)
            {
                return oRadio[i].value;
            }
        }
        return '';
    }

    function inArray(needle, haystack) {
		return (haystack.indexOf(needle) >= 0);
    }

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    CountriesCalendar.defaults = {
        elId : '',
        data: countriesCalendar,
        monthsEngShort: ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'],
        display:6
    }
    
    window.CountriesCalendar = CountriesCalendar;
})();