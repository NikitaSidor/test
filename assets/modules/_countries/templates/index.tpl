<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<h1>
    <i class="fa fa-globe"></i> Страны
</h1>

<div id="countriesModifier" class='container' v-cloak>
    <div class="item" :id="'cmItem-'+cCode" v-for="(cData, cCode, index) in countries">
        <div class="row countryInfoRow">
            <div class="align-self-center name col-5 col-lg-3">
                <span class="flag" :class="'fl-'+cCode"></span>
                {{cData.pagetitle}} <small>({{cData.contentid}})</small>
            </div>
            <div class="align-self-center col-4 col-lg-2">
                от <input type="text" class='w5digits' v-model="cData.info.price_from" @input="updatePriceFrom(cCode)"> <i class="fas fa-ruble-sign"></i>
            </div>
            <div class="align-self-center col-4 col-lg-2">
                <button class="visa btn" v-bind:class="{'btn-success': cData.info.visa==0, '': !cData.info.visa}"  @click="toggleVisaState(cCode)">
                    <template v-if="cData.info.visa == 0">
                    Безвиз
                    </template>
                    <template v-else>
                    Виза
                    </template>
                </button>
            </div>
            <div class="align-self-center col-4 col-lg-2">
                <button class="visa btn btn-secondary" @click="toggleTemperatureRow(cCode)">
                    <i class="fas fa-folder-open"></i> <sup>o</sup>C
                </button>
            </div>
        </div>
        <!-- <div class="tourTypesRow d-flex">
            <div>
                <button class="visa btn btn-info">Горнолыжный отдых</button>
            </div>
            <div>
                <button class="visa btn btn-info">Море и пляж</button>
            </div>
            <div>
                <button class="visa btn btn-info">Экскурсии</button>
            </div>
        </div> -->
        <div class="row temperaturesRow" v-bind:class="{'d-none' : !cData.displayTemperatureRow }">
            <div class="weatherIcons">
                <div><i class="fa fa-sun"></i></div>
                <div><i class="fa fa-water"></i></div>
            </div>
            <div class="month col-2 col-lg-1" v-for="(tData, monthName) in cData.temperature">
                <div class="month">
                    {{tData.ruShortName}}
                </div>
                <div class="air">
                    <input type="text" v-model="tData.air" @input="updateTemperature(cCode,monthName,'air')">
                </div>
                <div class="water">
                    <input type="text" v-model="tData.water" @input="updateTemperature(cCode,monthName,'water')">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var countriesInfo = [+countriesJson+];
var timeout = null;
var countriesModifier = new Vue({
    el: '#countriesModifier',
    data: {
        countries: countriesInfo,
    },
    methods: {
        toggleTemperatureRow: function(cCode){
            this.countries[cCode].displayTemperatureRow = !this.countries[cCode].displayTemperatureRow;
        },
        toggleVisaState: function(cCode){
            this.countries[cCode].info.visa = this.countries[cCode].info.visa == 0 ? 1 : 0;
            postJSON({
                action:'changeCountryVisaState',
                data: {
                    countryCode:cCode,
                    visaState:this.countries[cCode].info.visa
                }
            });
        },
        updatePriceFrom: function(cCode){
            this.countries[cCode].info.price_from = this.countries[cCode].info.price_from.replace(/\D/g,'');
            var price_from = this.countries[cCode].info.price_from;
            if ((!isNaN(parseFloat(price_from)) &&
            isFinite(price_from) &&
            price_from >= 0) || price_from=='') {
                if (price_from == 0)
                    price_from = '';
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    postJSON({
                        action:'changeCountryPriceFrom',
                        data: {
                            countryCode:cCode,
                            priceFrom:price_from
                        }
                    });
                }, 700);
            } else
                this.countries[cCode].info.price_from = '';
        },
        updateTemperature: function(cCode,monthName,type){
            this.countries[cCode].temperature[monthName][type] = this.countries[cCode].temperature[monthName][type].replace(/[^0-9-]/g, '').replace(/(?!^)-/g, '');
            var t = this.countries[cCode].temperature[monthName][type];
            if ((!isNaN(parseFloat(t)) && isFinite(t)) || t == '') {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    postJSON({
                        action:'changeCountryTemperature',
                        data: {
                            countryCode:cCode,
                            month:monthName,
                            type:type,
                            temperature:t
                        }
                    });
                }, 700);
            } else
                this.countries[cCode].info.price_from = '';
        }
    }
});

function postJSON(data,url=window.location.href) {
    fetch(url, {
        method: 'post',
        body: objectToFormData(data),
    });
}
function objectToFormData(obj, form, namespace) {
    var fd = form || new FormData();
    var formKey;  
    for (var property in obj) {
        if (obj.hasOwnProperty(property)) {
            if (namespace)
                formKey = namespace + '[' + property + ']';
            else
                formKey = property;
            if (typeof obj[property] === 'object' && !(obj[property] instanceof File))
                objectToFormData(obj[property], fd, property);
            else
                fd.append(formKey, obj[property]);
        }
    }
    return fd;      
}
</script>