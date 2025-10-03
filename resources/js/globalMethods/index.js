// src/globalMethods.js
import moment from 'moment';

export const globalMethods = {
    methods: {
        addOne(value) {
            if (isNaN(value)) {
                return '0';
            }
            let val = value + 1;
            return val;
        },
        age(value) {
            return moment().diff(moment(value, "DD MMM YYYY"), 'years');
        },
        currency(value) {
            if (isNaN(value)) {
                return '₦ 0.00';
            }
            let val = (value / 1).toFixed(2).replace(',', '.');
            return  '₦ '+val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        dateDay(text) {
            if(text == null){return '';}
            return moment(text).format('DD');
        },
        dateCompareToday(date, query){
            var test_date = new Date(date);
            
            var today = new Date();
            today.setHours(0,0,0,0);
            if (query == '='){return (test_date == today);}
            else if (query == '<'){return (test_date < today);}
            else if (query == '<='){return (test_date <= today);}
            else if (query == '>'){return (test_date > today);}
            else if (query == '>='){return (test_date >= today);}
        },
        dateGreaterThanToday(text){
            var test_date = new Date(text);
            var today = new Date();
            today.setHours(0,0,0,0);
            return (test_date >= today);
        },
        dateLessThanToday(text){
            var test_date = new Date(text);
            var today = new Date();
            today.setHours(0,0,0,0);
            return (test_date < today);
        },
        dateMonth(text) {
            if(text == null){return '';}
            return moment(text).format('MM');
        },
        dateToday(){
            return new Date().toJSON().slice(0, 10);
        },
        dateYear(text) {
            if(text == null){return '';}
            return moment(text).format('YYYY');
        },
        ExcelDate(text) {
            return moment(text).format('Do MMMM, YYYY');
        },
        ExcelDateShort(text) {
            return moment(text).format('DD/MM/YYYY');
        },
        ExcelDateMonth(text) {
            return moment(text).format('MMM Do');
        },
        Excel6Months(text) {
            return moment(text).add(6, 'M').format('MMM Do, YYYY');
        },
        ExcelMonthYear(text) {
            return moment(text).format('MMM, YYYY');
        },
        FullName(text) {
            if (text == null) {
                return 'Old User/Staff';
            }
            return text.last_name + ', ' + text.first_name + (text.middle_name != null ? ' ' + text.middle_name : '');
        },
        FullDate(text) {
            return moment(text).format('LLLL');
        },
        firstUp(text) {
            return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
        },
        getAge(text) {
            var birthYear = parseInt(moment(text).format('YYYY'));
            var currentDate = new Date();
            var currentYear = currentDate.getFullYear();
            var age = currentYear - birthYear;
            return age + ' years';
        },
        Names(text) {
            if (text == null) {
                return 'Old User/Staff';
            }
            return text.first_name + (text.middle_name != null ? ' ' + text.middle_name : '');
        },
        profilePicture(text) {
            if (text == null) {
                return '/img/profile/default.png';
            } else {
                return '/img/profile/' + text;
            }
        },
        readMore(text, length, suffix) {
            if (text == null) {
                return text;
            } else if (text.length <= length) {
                return text;
            } else {
                return text.substring(0, length) + suffix;
            }
        },
        shortDate(text) {
            return moment(text).format('MMM Do, YY');
        },
        timeDifference(start, end, format, category = 'Calendar') {
            /*var timeBegin = moment(start);
            var timeEnd = moment(end);

            return (timeEnd.diff(timeBegin, format) - 1)+' '+format;*/
            if (start == null || end == null ){
                return 0+' '+format;
            }
            else if (start == end){
                return 1+' '+format;
            }
            else{
                let date1 = new Date(start);
                let date2 = new Date(end);
                //var leave_type = this.leave_types.find(obj => obj.id === this.leaveRequestData.leave_type_id);
                //if (leave_type == null || leave_type == undefined){ return 0;}
                //else{
                if (category == "Calendar"){
                    let Difference_In_Time = date2.getTime() - date1.getTime();
                    let Difference_In_Days = Math.round(Difference_In_Time / (1000 * 3600 * 24));
                    Difference_In_Days = Difference_In_Days+1;
                    return Difference_In_Days+' '+format;
                }
                else if(category == "Working"){
                    var day;
                    var current = date1;
                    var totalBusinessDays = 0;
                    while (current <= date2) {
                        day = current.getDay();
                        if (day >= 1 && day <= 5) {++totalBusinessDays;}
                        current.setDate(current.getDate() + 1);
                    }
                    return totalBusinessDays+''+format;
                }
            }
        },
        treatFont(text) {
            let story = text.replaceAll("font-size: 1rem", "font-size: 2rem");
            return story;
        },
    },
};
