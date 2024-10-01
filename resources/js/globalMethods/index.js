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
            return moment(text).format('DD');
        },
        dateMonth(text) {
            return moment(text).format('MM');
        },
        dateYear(text) {
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
        timeDifference(start, end, format){
            var timeBegin = moment(start);
            var timeEnd = moment(end);

            return timeEnd.diff(timeBegin, format)+' '+format;
        },
        treatFont(text) {
            let story = text.replaceAll("font-size: 1rem", "font-size: 2rem");
            return story;
        },
    },
};
