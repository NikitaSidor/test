<!-- <script src="https://cdn.jsdelivr.net/npm/vue"></script> -->
<script src="https://unpkg.com/vue@2"></script>
<h1>
    <i class="fa fa-globe"></i> Страны
</h1>

<div id="countriesModifier" class='container' v-cloak>
    <div class="item" :id="'cmItem-'+cCode" v-for="(cData, cCode, index) in countries">
        <div class="row countryInfoRow">
            <div class="align-self-center name col-4 col-lg-4">
                <span class="flag" :class="'fl-'+cCode"></span>
                {{cData.pagetitle}} <small>({{cData.contentid}})</small>
            </div>
            <div class="align-self-center col-2 col-lg-2">
                от <input type="text" class='w5digits' v-model="cData.info.price_from" @input="updatePriceFrom(cCode)"> <i class="fas fa-ruble-sign"></i>
            </div>
            <div class="align-self-center col-2 col-lg-2">
                <button class="visa btn" v-bind:class="{'btn-success': cData.info.visa==0, '': !cData.info.visa}"  @click="toggleVisaState(cCode)">
                    <template v-if="cData.info.visa == 0">
                    Безвиз
                    </template>
                    <template v-else>
                    Виза
                    </template>
                </button>
            </div>
            <div class="align-self-center col-2 col-lg-2">
                <button class="btn btn-secondary" @click="toggleTemperatureRow(cCode)">
                    <i class="fas fa-folder-open"></i> <sup>o</sup>C
                </button>
            </div>
            <div class="align-self-center col-2 col-lg-2">
                <button class="visa btn btn-secondary" @click="toggleTourTypesRow(cCode)">
                    <i class="fas fa-folder-open"></i> Типы туров
                </button>
            </div>
        </div>
        <div class="tourTypesRow row" v-bind:class="{'d-none' : !cData.displayTourTypesRow }">
            <div v-for="tourType of tourTypes">
                <button class="btn"  v-bind:class="{'btn-info' : isSelectedTourType(cCode,tourType.alias) }" @click="toggleTourTypesState(cCode,tourType.alias)">{{tourType.pagetitle}}</button>
            </div>
        </div>
        <div class="tourTypesMonthsRow" v-bind:class="{'d-none' : !cData.displayTourTypesRow }" v-for="tourType of cData.tourTypes">
            <div class="row">
                <div class="tourTypeName">{{tourTypeRuName(tourType.tour_type_alias)}}</div>
                <div class="months">
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(12) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,12)">Дек</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(1) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,1)">Янв</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(2) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,2)">Фев</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(3) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,3)">Мар</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(4) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,4)">Апр</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(5) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,5)">Май</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(6) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,6)">Июн</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(7) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,7)">Июл</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(8) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,8)">Авг</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(9) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,9)">Сен</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(10) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,10)">Окт</div>
                    <div class="btn" 
                        v-bind:class="{'btn-info' : tourType.month.includes(11) }"
                        @click="toggleTourTypesMonthState(cCode,tourType.tour_type_alias,11)">Ноя</div>
                    <div class="btn" 
                        @click="toggleTourTypesMonthStateAll(cCode,tourType.tour_type_alias)">Все</div>
                </div>
            </div>
        </div>
        <div class="temperaturesRow" v-bind:class="{'d-none' : !cData.displayTemperatureRow }">
            <div class="weatherIcons">
                <div><i class="fa fa-sun"></i></div>
                <div><i class="fa fa-water"></i></div>
            </div>
            <div class="row">
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
</div>

<script>
var tourTypes = [+tourTypes+];
var countriesInfo = [+countriesJson+];
var timeout = null;
var countriesModifier = new Vue({
    el: '#countriesModifier',
    data: {
        countries: countriesInfo,
        tourTypes: tourTypes,
    },
    methods: {
        toggleTemperatureRow: function(cCode){
            this.countries[cCode].displayTemperatureRow = !this.countries[cCode].displayTemperatureRow;
        },
        toggleTourTypesRow: function(cCode){
            this.countries[cCode].displayTourTypesRow = !this.countries[cCode].displayTourTypesRow;
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
        toggleTourTypesState: function(cCode,tourTypeAlias){
            var founded = false;

            for (j=0;j<this.countries[cCode].tourTypes.length;j++)
            {
                if (this.countries[cCode].tourTypes[j].tour_type_alias == tourTypeAlias)
                {
                    founded = true;
                    this.countries[cCode].tourTypes.splice(j, 1);
                    break;
                }
            }

            if (!founded)
            {
                var array = this.countries[cCode].tourTypes;
                array.push({
                    month: [0],
                    tour_type_alias: tourTypeAlias
                });
                array.sort(function(a,b){
                    var aIndex = bIndex = null;
                    for (i=0;i<countriesModifier._data.tourTypes.length;i++) {
                        if (a.tour_type_alias == countriesModifier._data.tourTypes[i].alias)
                            aIndex = i;
                        if (b.tour_type_alias == countriesModifier._data.tourTypes[i].alias)
                            bIndex = i;
                        if (aIndex !== null && bIndex !== null)
                            break;
                    }
                    if (aIndex < bIndex)
                        return -1;
                    if (aIndex > bIndex)
                        return 1;
                    return 0;
                });
                this.countries[cCode].tourTypes = array;
            }
            
            for (i=0;i<this.countries[cCode].tourTypes.length;i++) {
                for (k=0;k<tourTypes.length;k++) {
                    if (tourTypes[k].alias == this.countries[cCode].tourTypes[i].tour_type_alias){
                        this.countries[cCode].tourTypes[i].tour_type_id = parseInt(tourTypes[k].id);
                        break;
                    }
                }
            }

            postJSON({
                action:'changeCountryTourTypesState',
                data: {
                    countryCode:cCode,
                    tourTypes:JSON.stringify(this.countries[cCode].tourTypes)
                }
            });
        },
        toggleTourTypesMonthState: function(cCode,tourTypeAlias,month){
            for (j=0;j<this.countries[cCode].tourTypes.length;j++)
            {
                if (this.countries[cCode].tourTypes[j].tour_type_alias == tourTypeAlias)
                {
                    var mIndex = this.inArray(month, this.countries[cCode].tourTypes[j].month);
                    if (mIndex === false)
                        this.countries[cCode].tourTypes[j].month.push(month);
                    else
                        this.countries[cCode].tourTypes[j].month.splice(mIndex, 1);
                    break;
                }
            }

            if (this.countries[cCode].tourTypes[j].month.length > 1)
                for (i=0;i<this.countries[cCode].tourTypes[j].month.length;i++)
                    if (this.countries[cCode].tourTypes[j].month[i] == 0)
                        this.countries[cCode].tourTypes[j].month.splice(i, 1);

            postJSON({
                action:'changeCountryTourTypesState',
                data: {
                    countryCode:cCode,
                    mode:'month',
                    tourTypes:JSON.stringify([this.countries[cCode].tourTypes[j]])
                }
            });
        },
        toggleTourTypesMonthStateAll: function(cCode,tourTypeAlias){
            // for (i=1;i<13;i++)
            // {
                
            // }
            for (j=0;j<this.countries[cCode].tourTypes.length;j++)
            {
                if (this.countries[cCode].tourTypes[j].tour_type_alias == tourTypeAlias)
                {
                    console.log(this.countries[cCode].tourTypes[j]);
                    console.log(this.countries[cCode].tourTypes[j].month);
                    console.log(this.countries[cCode].tourTypes[j].month[0]);
                    if (this.countries[cCode].tourTypes[j].month[0] == 0)
                        this.countries[cCode].tourTypes[j].month = [1,2,3,4,5,6,7,8,9,10,11,12];
                    else
                        this.countries[cCode].tourTypes[j].month = [0];
                    break;
                }
            }
            console.log(j);
            console.log(this.countries[cCode].tourTypes);
            console.warn(this.countries[cCode].tourTypes[j]);
            postJSON({
                action:'changeCountryTourTypesState',
                data: {
                    countryCode:cCode,
                    mode:'month',
                    tourTypes:JSON.stringify([this.countries[cCode].tourTypes[j]])
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
        },
        tourTypeRuName: function (tourType) {
            for (i=0;i<this.tourTypes.length;i++)
            {
                if (this.tourTypes[i].alias == tourType)
                    return this.tourTypes[i].pagetitle;
            }
        },
        isSelectedTourType: function (cCode, alias) {
            for (i=0;i<this.countries[cCode].tourTypes.length;i++)
            {
                if (this.countries[cCode].tourTypes[i].tour_type_alias == alias)
                    return true;
            }
            return false;
        },
        inArray: function(needle, haystack) {
            var length = haystack.length;
            for(var i = 0; i < length; i++) {
                if(haystack[i] == needle) return i;
            }
            return false;
        }
    }
});

function postJSON(data,url=window.location.href) {
    fetch(url, {
        method: 'post',
        body: objectToFormData(data),
    }).then((resp) => resp.json());
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