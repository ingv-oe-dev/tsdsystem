function generatePalette(len) {
    var paletteArray = [];
    for (var i = 0; i < len; i++) {
        var gen = generateColor();
        paletteArray.push({
            "backgroundColor": gen,
            "color": invertColor(gen, true)
        })
    }
    return paletteArray;
}

function generateColor() {
    const hexArray = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F'];
    let code = "";
    for (let i = 0; i < 6; i++) {
        code += hexArray[Math.floor(Math.random() * 16)];
    }
    return `#${code}`
}

function invertColor(hex, bw) {
    if (hex.indexOf('#') === 0) {
        hex = hex.slice(1);
    }
    // convert 3-digit hex to 6-digits.
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    if (hex.length !== 6) {
        throw new Error('Invalid HEX color.');
    }
    var r = parseInt(hex.slice(0, 2), 16),
        g = parseInt(hex.slice(2, 4), 16),
        b = parseInt(hex.slice(4, 6), 16);
    if (bw) {
        // https://stackoverflow.com/a/3943023/112731
        return (r * 0.299 + g * 0.587 + b * 0.114) > 186 ?
            '#000000!important' :
            '#FFFFFF!important';
    }
    // invert color components
    r = (255 - r).toString(16);
    g = (255 - g).toString(16);
    b = (255 - b).toString(16);
    // pad each with zeros and return
    return "#" + padZero(r) + padZero(g) + padZero(b) + '!important';
}

function padZero(str, len) {
    len = len || 2;
    var zeros = new Array(len).join('0');
    return (zeros + str).slice(-len);
}

/* 
    Generated colors (1000) for nets and sensortypes.
    If you want to change the palette run the following command:
    > colors = generatePalette(paletteLength);
    Set the preferred paletteLength for you needs.
*/
const paletteLength = 1000;

const colors = {
    "nets": [{
            "backgroundColor": "#808080",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FB4A26",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#35140E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E4CA53",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#DE6833",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7986D6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#706E80",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3556FD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B88DC2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E3994B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AA948D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3FEBF4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#264F9A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B31C8F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A565A6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EB97F8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#5A3C74",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#84479F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#86AD9B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1DBC9A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#897B90",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FE3B7B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FA16C2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#846E42",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ABC215",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#77748C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BECB3B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DA80DF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F55DA3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#04EF6F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B8500E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B4EC82",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#9B5B74",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A0A98F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ACDF44",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#1D40B1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C2B634",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#633445",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5D7FBC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#021183",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#089C92",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5CB8F0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#13A506",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8B3A6C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CB1FDA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6C3830",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ED56ED",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#03D2EA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7B5503",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EB530D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#92494D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1F7C18",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#50F966",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CB4225",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B799E5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2AF07C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#48E397",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#91D208",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AEF709",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#17D2DA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D2B7F6",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#5CD313",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D96219",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D6A5C4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F484FF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#01750E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E063F1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6EFD15",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5EC5D9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#340186",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#665D17",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#07F8F3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A48B44",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#82C859",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6C458F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DE336F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#86A161",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2E9FAC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C9058A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A6610C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C12EED",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8AC6D5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E2BE53",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D4C827",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#13E8C2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#538536",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0322B7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AA3A65",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6EC1B3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CFDB89",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C7EC35",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#9046AC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BB8ADB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#852DEC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AF6562",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#33C52F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#18FB63",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C19AF8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7C9EC3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#16C034",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A9936B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4A653D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C25395",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D5A2CE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#84A59E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A5FD3F",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D81C79",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C15DC4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CB6895",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D6E8F8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#F803BB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#02E614",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#38EC5A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A05B89",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8F647A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5960B8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1E85E1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1221C6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6E7AFD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9344D4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0C5098",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FFEA6D",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#2A7B6F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2CD79B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#16CC1E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0878D5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5A3419",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F5B0F8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C98DCF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#158BA1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AC7683",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#21AE03",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6F61AB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7C2172",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#84DAA3",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#62E0E8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#3EF84D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EA9BD8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B54621",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C75209",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2CBC08",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B6DCD7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#585C66",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3106B9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D47F79",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5F80D3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#500273",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4DA94A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8C6DEE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0C594F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E69664",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#160ABA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C3FE36",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#9B8A03",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8F753F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D16FC0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#87881C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A5252C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EAA0B7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3EA20E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#385B73",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E548CD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#40024F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6F6A2C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8705C0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#07CAA5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#697D8C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#10AEBE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B620C3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3A14ED",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#62848C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E55B3D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C874CC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E2C201",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7EC0CD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3B0F47",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#342ED2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B30E4A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4757F5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C24510",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B39D8F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2036B9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4A7C66",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#36E462",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CC686F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DCE4E6",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#73C08A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#616557",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#46BEA6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#03737A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#55239A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C197E5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7A20BB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A13FE6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4688ED",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#44D8E7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BD9C3E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0540A0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E3915C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#00DEF9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A1CB7C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DE3919",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E7A054",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#61F617",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#05C145",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3172B4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F0B168",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D82928",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4E55A3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BF1242",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5521C6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E1AFCD",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#4CFF90",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#473862",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8BA9EF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#40C0EA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DE4699",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C5F7A3",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#079D0D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3B1724",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DE7749",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D4004C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#513208",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8B95A5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#392E4B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5D0383",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EF0001",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4BF749",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1057A0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DE56C8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BF32DE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#211E92",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#05B65C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#211878",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EA4633",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#095027",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#45784B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#81CC2E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#34741C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B197A1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CD15DD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DCCD20",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#367117",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B939E8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D389A9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6FD56F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8A35AD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E501E0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5D1320",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4C3160",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C969F2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#76145D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6A1B8F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BE05EC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EB8340",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2593FD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C3DB06",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C5D0F7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#98981C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#170D29",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DF3C46",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DA3BE7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7AC3DE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5AD95D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9CFCB2",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#012322",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7DA35F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#67CA48",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8D5492",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3A873A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C4FD81",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#A1A7F7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5BBE79",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1B2F41",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3EBDB0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FE15B9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3B2EBA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8D4D21",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3B1648",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AFD421",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#383732",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2D7654",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4A8BB2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#536120",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#54FA87",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#8FE13D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#737B54",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F0836E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EC00F8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A9027E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5FCE04",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9898F7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#13FBC1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F15A1A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A06954",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E9A4F8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#EC2555",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#470BA3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#16C2D6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7C8064",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#98661C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9EBAC3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BDE319",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D7C9AD",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#1C933D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#53F715",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CEE5B8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#CFF57A",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#DA8D5F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#71F44A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3DD3E5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#213381",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5F7E2C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#847B92",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#91C52D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5E7A32",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8ADBDE",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#FBB960",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#1310F9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E0EAD3",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#5A0334",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#106B5A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B5E25F",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#127298",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1A0A08",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#25FD3B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#332EC0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E5E31D",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#92EBF7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#AAB976",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#59C34E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EFB2FB",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#50FFBD",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#9C1582",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7BF08F",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#376E1C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#106D84",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3CD3BA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F553FA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FBACC4",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#0DBEE0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2E0B41",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#558B5D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7B67F2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#34962B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3876EA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7E4E48",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#66FBD8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#4A464D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#948707",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#061CE8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DD64A0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#02BE2D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FC4308",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#075669",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A70CDC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#74D262",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9EF4AF",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#66E3D9",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#4896F6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#86CDED",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#ED678C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#124C11",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E5B9C1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#104652",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#451A8C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2C2D96",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8BB6CE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#73B60F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#73F7C3",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#F24570",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#386043",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F06584",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E119ED",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#572A8D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EC0722",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C9B695",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F66149",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3B2122",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8438B6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C01359",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F25084",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#414C41",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#577937",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C1150E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#970022",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7AE66C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#39F4A8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5AE617",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8B569B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#64A2C9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FFFAF8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#595814",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#42FCCA",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#72452A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#430530",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#77F65E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#834C85",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#20E609",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#23AA33",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F56A99",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#957D4F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#765850",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DB3316",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3BEF88",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#434E33",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B0C6D7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#A33909",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6965DD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#071636",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#524FB0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F7902A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BE9C40",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#27BA43",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FB0ECC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#49FFF7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#CF018D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5DA86B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#60B748",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5F0041",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2EE54C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#269E03",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C8E6A9",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#BFA328",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1D79D8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1563CE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7FC165",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CE0B08",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D628AD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#578373",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CA6514",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3E8149",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5C6169",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#41235E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5A4EDB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A28306",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FCF3A0",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#0DAF4E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0515F3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ED5DB3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#59967F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#415DCC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#612B23",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1FFF9C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#98779C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8792F9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#367864",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AC60CF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D74A25",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C50101",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2B3DFE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8DB00D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0624F3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4C7027",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#247954",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#684A18",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1A8708",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#65A8C7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#341DC7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3B4F1D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#46F955",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A87776",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2B073E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7DC21E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#34BF2B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D62909",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7F252E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#498DB1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9B37A1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2F152D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B9061A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7C5114",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E56496",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5A48BB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C40AB5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3665BA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EBC83E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#509605",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B4F874",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#E6F020",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#12B1DC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6FC14B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4D3867",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#686043",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#422761",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#345A5B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4535B8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#56FB1D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F8AA5B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C02ABD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#370965",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7746F0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9C2426",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CE071F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3834A6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1C8427",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B5649E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#531549",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7AED00",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0515DD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#62ABE2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A2C4CB",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#178DE3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2C7B0A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#09C95B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5838E4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CB32D5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1DF571",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#01AA5E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B26B00",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C07981",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#82DD66",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FFB95F",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#BDC664",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#32A247",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D41E4F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1B3E96",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#14EA6B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1D7E8D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#15F94A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1C0339",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#31A50B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0AEA6D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9ACB6C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A3431A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9BAE4E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E4D2C4",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#5A910F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#682A3B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B5D939",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C68B2E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#439608",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C1A9F7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B10197",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A6595D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#629251",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#87F8C2",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#438AD4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4E6E0D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#17E458",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FB1CE9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#26805C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CA4F26",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E8D0D3",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D63EC7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6CE6BC",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#B921E7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1AD2F7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B69643",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1EAAB0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F9F5F9",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#8B8833",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9443B0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#122D9E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5B86C7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#904109",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#699ACF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#383174",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D8858C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8EBD11",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DFE1FB",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#38A745",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#31AB0A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#77599B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FD59D8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#17B2B1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6EA1D3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D19E29",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CA6451",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#22B340",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D86820",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#42F74B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F592CB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#420E4A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#41B96A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4F5D88",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#77B2F4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#181AE0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#874555",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1455A8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#29ED91",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#98723E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8066DC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AFB4C6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9B6522",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0D95C7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C85417",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7A80CA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A76ACA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E7EA21",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#57D504",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#141669",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#683952",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C22BC3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4B5580",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#90962A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6EA29F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8CBEA4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FAFE26",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#1620A6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F09B2F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6205EE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0E0E00",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#87ED46",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D3AD2B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E18A7D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#578D82",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#367508",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F16A28",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5CECA6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5E3ECA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#64DC65",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#68F29C",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#1C301E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D46705",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EA5B66",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#602E5A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1112CB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#98551D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#93CA00",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D2B56E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F50D17",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5C5272",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1EA869",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6E9825",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#329AA9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E67C1E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#54C3BE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#166B81",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FDF10E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#B1CAFA",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#3763FC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#89641E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4D35FC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#229D02",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#338AFE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#91902C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2E28BF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#46278B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6A24B5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9719CE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#68FFC0",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C9B634",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A4058D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DD1C43",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4020B9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#013E42",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A1A6FD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C05D0A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#48AE9E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3A1815",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#78289F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EB8D71",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7A79CC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#77F4C7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#433E13",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#29FE02",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1BBA4D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8396F8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#136116",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#910CD4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#30AA54",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D2E9D7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#9D2B8D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F771D0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E2ADB1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D1DEFC",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#9B3324",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7CD954",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FD4C46",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F22B13",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F121C9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FBF483",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C686C5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A1C149",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#38A891",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#268CFE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3D3E61",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#17DA0F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#87901A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BA69D6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A39D8A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4A22F2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C6D5CA",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#996CEF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#61CC66",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2F0A4F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D4A232",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#83B528",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8F7459",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0953BD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A0F416",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#6F515F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B3AD2A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2491A5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CAA547",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#342F53",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#288F7B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#044E57",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#65DA13",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E9054F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#311BEC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F67FFE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8B200A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9028F6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#218E6B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#74870E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#82CB2A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#756504",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C9A480",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5F19E8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ED8B11",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#22DC14",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#165DE9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5C2159",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0F84A1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#21BE88",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D8812B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CEDE0A",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#512587",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C925B6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D347A4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AC1E12",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F2FE0A",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#7A9575",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ECF216",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#37601C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#87752A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2BE102",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#09D8E7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ECCFE2",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#981E07",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#300FD8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6A62A2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AC4EAA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0C8DB0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#69D168",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#48724D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FDEAC4",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#340E97",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8F7A10",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#98D831",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A230A4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FAEA8F",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#44DA3F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E05F47",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2499E9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#978DBB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DC0CC5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#31EEE0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#660A4B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BEEEE0",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#012021",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#032F1E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#89CFCC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#88ADA7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A632A2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2D565B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#12F5DF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3C0545",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#96C116",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CED63E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#E011F9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C19A44",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D6597D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A47F51",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FEE0A1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C666A4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#07A5DD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9BD160",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#49F61D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#659E56",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#83B7D2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C09715",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2B20DE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1272AE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#732E61",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#411F89",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7ABDCD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#76776E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B4E7A2",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#3D6F78",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5DB1AC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F5D77D",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#A1ED37",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#F2AB0D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#40D1EA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3A7AB8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#135A77",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B7A736",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D80253",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7C9D1E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AEDE0F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#38C0B6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6578C3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3FDA7A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#842AA8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F05CB2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#94FB60",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#F91886",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8C4D95",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#03EB73",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E6979F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FEC44B",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#F40B64",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A4389F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C8D42D",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#41CBC6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E77386",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#46D73A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#81D862",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FF217F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#71E6B8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#966FD1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6BF2C7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#90534C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2EFC73",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C8DD19",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#5EA246",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#72E163",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#435071",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#40623F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D95F95",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B55A90",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1A2698",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CB0B4E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#57B7DB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AD5157",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7B6601",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#404739",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C91E86",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E5D089",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#8F9B42",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B48EA6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CD6A2D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B23420",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#13FF4D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#49BD5C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FC3B5B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#343E5C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#51BAB9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F0FEF2",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#DDA80A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2110AA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B8DA86",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#6B379A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5BDF62",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F026B9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C657AC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AF3314",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A83917",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BB7206",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5D7CBF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1377F0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4093CB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8A4DD3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6E943D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8A8CDF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BF4AF5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FA855A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#375AD4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#902705",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B95990",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#43A7DF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#944F46",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C55AF4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EC8996",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#52192A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9B0192",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2FEBDB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#63C128",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3C75EA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CE0868",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DE148F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#52FFA1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#875EA8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2E7B5D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A1E23E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#EF0983",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9487F7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#044851",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#91627A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A94EA4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9A97BD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9A47D6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4EFF97",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#DCD9A6",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#7E2196",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C63CE0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DB0833",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4ECDBE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7A481E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1C3A60",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B3CDA6",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D60967",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#60293A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C4DDF2",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#6B4248",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#502EE9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9C57E7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E32CD6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E517E7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E02A27",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#657A42",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#748309",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5F0BE7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1B16E8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ADBAA5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#455079",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#10B355",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0D64F4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6496AC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#82049B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B9D7B5",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#189B9F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9683EB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5F0ABF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1998ED",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#482C34",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B7B72A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8F2A98",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#23A3E7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DC04C5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E00B57",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B13853",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#418120",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6D38C5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5FBBC7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9DB021",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#40EE3D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6BC3C8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4F9936",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#53445E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1D768F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7D7020",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4A17AB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C8E790",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#FA4F01",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F4F447",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#2893FF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BEC72D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#61AEA8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#231A47",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6BF03B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#963847",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C003BA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#147A92",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5647BF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#117800",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5B6C26",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AAC677",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A0AA5A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FE80C6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AD2CA3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4B4AB2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A5B322",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#29DB34",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#17F080",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B6E748",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C8D469",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#545BA0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DD4442",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#355464",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D9AD63",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#37F5C6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#41189D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0CBF18",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D7AE7B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A03B9C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D4495A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9BF0E6",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#3CF23A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1DE7A0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E5B1F5",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#19BA64",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#639799",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CCBF66",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A1079D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BC6FB5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#56A4AD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BFA401",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7DC6CC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7B2FF7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3ECAB7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1AA842",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#36FD7C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#202658",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2B7A6D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#80FC1A",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#084293",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#24F25A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#690A0E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#533384",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B4F463",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#CA2347",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4222FB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D6BA41",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A96EEE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3D073D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#35A9AB",
            "color": "#FFFFFF!important"
        }
    ],
    "sensortypes": [{
            "backgroundColor": "#808080",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BA58E8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B00837",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B699B5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9F1567",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#51AE38",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8C6DCE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3E220A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AA27C8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1B64BE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8A3DFD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#124C4C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#08BF48",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4D6B8A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0D9FB3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0B390A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3FEA4D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#756974",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#311FB6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2EF636",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#97190B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0B7276",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EA91E9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A2DEF1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#5020CD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#019AEC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D8A209",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A5D51B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CDA45C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#056089",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F310F5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#668AF4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#29E339",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#91BE43",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#81B0FA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C006D9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ADAF49",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C3E2CB",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#A2AB9A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#72B203",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#025FCA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1E8066",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BED3F7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#BADD64",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#90A022",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#582E9E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B798CE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DEFFB7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#29555F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2B9741",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#091049",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B9F79B",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#456CB1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#133212",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5F61E1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F81F1E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DB5238",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#231D8D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9FE6A1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#567529",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CB38D1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CCF234",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#8C1743",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2DD06E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#04298B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B1B3E6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C8A055",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#291321",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5690CA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9553D9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7DE4FC",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#0BE34A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#245CD6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CCD8CB",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#27F90E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BF9DA9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#577A1D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5BE69C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#25D3DC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BFDCB8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#215012",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#353EC7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FF02AC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1AA57B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#119673",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DF634B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AF2B20",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#23DD45",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#08BDA6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4BAC3A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D07AB9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8875D6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#63D69E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#33EDD4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#184984",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A9D2D1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#95C75D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#57C833",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A91020",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#080ADE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2B4B2E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D67148",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9AC4C7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#419FD9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1B080D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#04693F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#44BEE9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2FE0DD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FD3180",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#74CA68",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B37668",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B93517",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3F5D5F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DAE711",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#637F53",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#95AB50",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7FCD9E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9CF191",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#CD38B0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#68D81A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6E8FAE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D0E83C",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#827109",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#491F9C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0A961C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E24BDE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9830F4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AE3712",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E1C001",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6057B4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8B2AB6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#759EEE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1EF488",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F993DD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3B92AF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DB5887",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2E4D20",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9BE6FF",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#F3DE7F",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#CFBE49",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BA5DFF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#921829",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#071543",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#723623",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A2092D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8A79BD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#47C66D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#66751E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A17732",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E9578A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B23A07",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#06DE95",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0F8B2A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#08D8B6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FD4C11",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#01F8DB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5DCE8A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#70546D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#656520",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2E3F18",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7ACA7F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6D1963",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3047B1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B9AE7C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7EBD0E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DA7C26",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C52581",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CE173A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C6019A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#70B3F9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B21D98",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#539BCC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#84F334",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#62F64E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#74C817",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BA7332",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#581A22",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E97439",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B3A28F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0D1689",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3DB9EA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BA3140",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D74F40",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0C7B2A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4B5CDE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F3CA52",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#44F7F3",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#EDBD37",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#258C29",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F89BC3",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#593AA3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EA43C4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#32518A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E6512C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B8F71E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#3E44F0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#145016",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FE341B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#10DF97",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#050B0A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#681B80",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#818D07",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ABC740",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#71F829",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AD5753",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5965D2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0E7D4D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#20D13C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8012AD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3F48CB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3688D0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7D3606",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0AB322",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#712756",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2BDC6B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#239909",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D53A58",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1031B1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F58549",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#10E865",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F103DE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4756CF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0AA7DF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D3EFFA",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#5EED12",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C6B226",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CD6A15",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2D348B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2F19F5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F706C7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1C375F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1F752F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#43210E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ED0470",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#85B25A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B0F740",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#4A1908",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FBBBF7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#F4B72D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A8636D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#315FE0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#889DD6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#263FE0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D524A0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4C88F5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#21A52C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D64F05",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8CC775",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ACC5C5",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#CCE89D",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C2CA70",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#6F0979",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#56251E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#85F484",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#17DD25",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7F6B7B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#17BFE3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9A3B83",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#46F1E1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#0CC89F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8E8C91",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D8833B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#930545",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#494C52",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DB2EA4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#040AA3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#01FAE8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AE1ED3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#97F682",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#2B1FF1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8C4F08",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#98ABF0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FA5BD4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7E657E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1BADD5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D4AFD1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#3CD0F6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#14E679",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FF5727",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E431D2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F112D7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EFDA0F",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#FDCB5F",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#41EEA9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#47B08F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4C21F2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#54E7DA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E55523",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B6E983",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#809E40",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EEC7DE",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#014D15",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5B2379",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F1449C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7F95CF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9C0E88",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#573F9C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C11EBB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A63B77",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B299AB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CACE1F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#24293E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#45CB0B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7A4F78",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A5CCD1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#AFA03A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FCF839",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#6D300B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#74310D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#81D800",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6062C6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2B9F3C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ADEFEB",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#286733",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A5A40E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AAB512",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FB7DBF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#535E70",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#27C35E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#519CE7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B7DEBD",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#83745B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#13B8FB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4999D1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#960E97",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DFF16C",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#4F72BD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6DE40E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#82EE70",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#08BE10",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1F252F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1F5605",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FC4B75",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#89E1EA",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#E031F0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#84DE0E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1C55F1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#829A1E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E2A347",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D6D449",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#1FF298",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6E46E5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#82E272",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#976ED7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#531760",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#73DAED",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#A344FC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4EC29D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8C66CE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1D451C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8ADD18",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#80E766",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#835CC4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#08254C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AAC5A2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4F5E0B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#884ACA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DA5B4E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C18658",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FD5B13",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2309B4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D12106",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FF639E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#287E87",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#10B078",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A61309",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3435DE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#66A270",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7C467A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D167FA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7CC156",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#550667",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#91CF6B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9173BB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3D0716",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9D6EE3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DE9093",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C72BBC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2CF6C4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#33328E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1B2316",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EC0C91",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2FB1F1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DCAF11",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EF4510",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C85772",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F6DAE4",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#9FF7B1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#CF1A04",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A46EBA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D3FA22",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#77A3AD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0D06AF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#778FAF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5C864E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3C5E27",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#018033",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8AC353",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C40128",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D974D2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8BC579",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D9C919",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3B57F9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#004831",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#30F496",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A13A11",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#24CCFE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#688829",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D7FCE7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#21873E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#33D97D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1D2676",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2943F7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#895C34",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#363758",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#11B1BF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#45B19F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3357E4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B8D83F",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#E112A0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7D8435",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D174CC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#585BE7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BDCCD5",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#7C107A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E1E0B6",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#392E89",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B61499",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0FD008",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6000A4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9161FB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1E6569",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4D498E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A3DE84",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#321587",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3F7B8B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B52DE9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#53FE2A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#120E0A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C1E48C",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#DB79A8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3DBB56",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A2ED73",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#56B25D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D08847",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E97493",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#21B563",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#306FBB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FF9BC1",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#F6162C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#12BC86",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#176FDC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#19869C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#42EB5E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#294747",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0A6279",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6352E0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1A7A81",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#71C9A7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3C6C77",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F75623",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A7240A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#150E28",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#361314",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6DCF8C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#75A525",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3158BA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DF0D08",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A4A7C4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FCD2AA",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#DD830C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B74E56",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A36F71",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4F201C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#627908",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7CBCCD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0BA7DA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5E5B9B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#747111",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4633DA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CD63B6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F496DC",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#5D68EA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#37C338",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B8E04A",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#5DE119",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9027BD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#569DDC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0CF247",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#66B850",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EBB1D9",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#B2A244",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C74F23",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#561EBC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DE9989",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2D02EA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DCB6BC",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#ED0E69",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B985EA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C75C47",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#78FE22",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#55AAF3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#39F060",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5CC42C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DA11B7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#93D949",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D2716F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6C76D5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A6D073",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C9BDF8",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#1AEB03",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#647BEF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#910259",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9C6E40",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6B7DC5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C9D618",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#0C1D9A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C37753",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FA1936",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AAB59D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#722B74",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#442E60",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FC0A7C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B9F1D3",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#2D9A92",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AB462E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EEEA9E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#B45786",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0EC7AD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8710C8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D86444",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F549FB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3307FD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8D93D2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#61588C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#13951F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4737EA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#800B8A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#556642",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#07A137",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F626DA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1AC58D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9B244D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3F5B95",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0AEF90",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F65633",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#036ECF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FFD839",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#E10349",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5A2CAD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A5FD04",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#DA304A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CFC7BB",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#225CF8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#872EDC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8AA510",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DFF396",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#5FA307",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#65F84C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E3D184",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#28A604",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9345A8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#172A69",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#81BACA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BC4535",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9BACE0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D604D3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#23A4C3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#83B470",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A3375C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#17F0B9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B22979",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#113EBB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1419DF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C1DD42",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#E3E28E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#438A35",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ACDDA9",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#DAAD02",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#195D04",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#367A2E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B81499",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8ADC3C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#832F08",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#589B9D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#733EE8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#336A9D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B7E28D",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#906CEC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9DEB6E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#3119DC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C75B42",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E0330B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#441B2E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BF50A0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#46CFC8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FFFAF0",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D80B92",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#23A0B0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C14E6C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E3AC64",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7D7CA6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5FD83F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#389BBB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B12CB5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7DD895",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BFD12F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6AA180",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#13D6D3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#83DE31",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BA4316",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#295AD3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F0392D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#29E460",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A1FE3E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#E20283",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4449D3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C1BCC5",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#8234B4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C56B73",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#07920D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A46322",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#691E25",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#00A4D4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BE3B05",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#472A00",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9C357D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3C4E3B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DD6D68",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BFF9ED",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#1676E9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D4735D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6D2B11",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3D5DD2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1C45B7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#95FC7A",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D3D680",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D768CD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#663EE2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8F26BA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A1650C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9BA0FD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6F70AD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#220563",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#702783",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9A51BC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AB7540",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#644A18",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#38D4B4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#51CE44",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#491783",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B5DEFD",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#24CC38",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6C0AF0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#652D49",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A75CD3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#754E55",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D33AD4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7651E1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#11BC16",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#81A547",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#523CC0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#25D1A1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E31005",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0EE0F6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#137660",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F997C9",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#1CC75A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9D59B4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#010F91",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#37AFAF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B7EC88",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#8EF12C",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#6E0206",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#462FDB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#590E36",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AA64ED",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#58F497",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#38DA20",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4C7869",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4604EC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6CECF4",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#24D577",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9C4559",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6928A3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6C1F84",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4E65D9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7859CA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4CF262",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#55F2E0",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C2E85B",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#230C7D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#457197",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#934C06",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FD7656",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F6F8A2",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C381DF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C6A97F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E3ACB7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#22A3C0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FEE830",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#55AC81",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C8C141",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B6FC31",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#F32057",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#164EB1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A37E7E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A93359",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FDA658",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2532BD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FAF490",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#932FA3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EF222F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EAD479",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#BF2D8E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4FF861",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#104BA0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3DBAE9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#079608",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#083D83",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D10CA7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8577B4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#97CB7D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7E1C75",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#91ED51",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#176EBF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#674F52",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DF0DDB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B76BC5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#80B56A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#676D55",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2DE544",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#56C934",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7A0DDB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1A36F3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3E4E62",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9E9C25",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EE4C40",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EDFF64",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#35A7B3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F94DD9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1A491C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#17AB85",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0436ED",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7F536F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D04DE0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8B7491",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F8C04E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D4F47E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C40C59",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#78B963",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#11C7AA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1451C8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6DB465",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6966A4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#468A34",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1E2302",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0B32E6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#81E64A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4DF7E6",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#9ECFC9",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#FC10DE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#91C002",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BBEF7D",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#572B53",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C46BC3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#64462D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D55853",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#91F3EB",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#EEA55C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#70075B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5B8E45",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CDD45B",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#A5E5ED",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#2EF304",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B6E1F9",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#471B71",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#220A93",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#02886F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E87C90",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#45536B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3AD6BE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A47404",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BA26EE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#63DEB9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C6F0DD",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#E73B47",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#08F945",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A9CDF3",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#B2E954",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#1F3D26",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#76DA3A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#10A737",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#74B4B6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#738B50",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#445719",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#894EDA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#201A63",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#63F5E2",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#7F79D4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#305D37",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8E4A66",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B58C76",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5537B4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B4DB68",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#12B34C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D7DDEC",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#E69017",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#309F62",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3DF6B2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C74BA6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F137E2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AFC52D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E91B8C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C9901C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#821E4A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4CC77F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B0CC0D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AD6E48",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1454DC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7491FA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A1423A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1CE258",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#88649A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0B2940",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0643C5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9130CB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#48C815",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7A8D51",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#08B3E9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#69D562",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#186903",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CB03A6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1457D4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F56C2B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F89EA0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C72A2B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6DB39F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9F3EA3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D129FF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AE36E3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#77F73F",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#917EC6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8D74F4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1DD85E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#881399",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C91FE9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BC00C7",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DA3EE6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FB5F12",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#26E134",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6EAFBF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#87C7D0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CAEF43",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#FB8B19",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A9DF61",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#A16D28",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E4E478",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#A52EB5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7D1DC0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6115CA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F2711B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0F3577",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9C8148",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E6453C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4424A4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6C16DE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0105E1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#201831",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#269FA0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#56B7C6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#116803",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BC5FEE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AA49A6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D2EC6B",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#847502",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0D6F96",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A923ED",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C76C19",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B873B4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9766E4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B526E2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#11A6C5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#80007D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#92E1C9",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#57042E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#991265",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0C1EB9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#212368",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D7719C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5F91AD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0A9B80",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DE906C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#051D93",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#67BC06",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2E1558",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#36D848",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A75E7A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8119AB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DD7587",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7C9916",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0B54C8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#14CC75",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EDE86D",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#6B0F1A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3FE1B1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#79A812",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F2AEA6",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#820459",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3E446A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3E8C0C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#89F37B",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#0684C5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#46DE14",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4359A8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E7021C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B9DF01",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#FC8CA8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FB7976",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#970DDD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2DCBF4",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#AB05F1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5444FB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#0C1E3D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#EA8FA2",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#238721",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#ABE982",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#C01BB9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#653A0B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F84937",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#990B2D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B79555",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8EB1EB",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#392A64",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1CDA18",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#036CFD",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3AF4FE",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#CA65BA",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#76BD5F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A7CB64",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#1A6FAC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A6ACF0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#86CBAE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#D31778",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6BDB85",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#8CFFB2",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#957ACE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#367C16",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E1B04A",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#066F9F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#13D388",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4131BE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9A3D7D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#4A15ED",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5FFFB0",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#707BE1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2B5D7C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2F9A1F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#754E23",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CB9F4D",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#38C561",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C45026",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A79397",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#2AE652",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FD175C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#47ECE5",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#60B93C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#829338",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E77B80",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#CFF961",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#38995E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7719B3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#45F439",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#5E93E9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#E2B655",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#A5D2A4",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#D28EB6",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#F421FE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7371C1",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FDC7BC",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#15B9FF",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#DF325C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#9D7088",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#7C4E0C",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#6A4521",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#BDE4A7",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#ED649E",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#11CD1F",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#88F595",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#2171F0",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#B810F8",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C74BF3",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#3C0ECE",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#C2F39E",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#3CDCC9",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#05196B",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#55D5EC",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#62A131",
            "color": "#FFFFFF!important"
        },
        {
            "backgroundColor": "#FBA4FA",
            "color": "#000000!important"
        },
        {
            "backgroundColor": "#80FBBD",
            "color": "#000000!important"
        }
    ]
}