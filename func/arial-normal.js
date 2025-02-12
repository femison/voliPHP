(function (jsPDFAPI) {
var font = "undefined";
var callAddFont = function () {
this.addFileToVFS("arial-normal.ttf", font);
this.addFont("arial-normal.ttf", "arial", "normal");
};
jsPDFAPI.events.push(['addFonts', callAddFont])
 })(jsPDF.API);