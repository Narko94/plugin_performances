function cReport(data, type) {
    if(debug && window.console) {
        if(typeof (type) === "undefined")
            type = 'log';
        switch (type) {
            case 'log': console.log(data); break;
            case 'info': console.info(data); break;
            case 'warn': console.warn(data); break;
            case 'error': console.error(data); break;
            default: console.log(data);
        }
        return true;
    }
    return true;
}

var typeConsoleLog = ['log', 'info', 'warn', 'error'];

function dR() {
    if(!debug || !window.console) return false;
    var type = (arguments[0] !== undefined && typeConsoleLog.includes(arguments[0])) ? arguments[0] : 'log';
    switch(type) {
        case 'log'  : console.log(arguments); break;
        case 'info' : console.info(arguments); break;
        case 'warn' : console.warn(arguments); break;
        case 'error': console.error(arguments); break;
        default: console.log(arguments);
    }
    return true;
}

function cGroup(type, group) {
    if(!debug)
        return;
    if(type === undefined)
        cReport('Ошибка в функции: cGroup, не задан тип', 'error');
    if(type === 'start')
        console.group(group);
    if(type === 'end')
        console.groupEnd();
}