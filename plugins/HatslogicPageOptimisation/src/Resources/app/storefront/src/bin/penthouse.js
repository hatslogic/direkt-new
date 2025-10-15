var penthouse = require('penthouse')
var puppeteer = require('puppeteer')
var fs = require('fs')

var args = process.argv.slice(2);

if (args.length < 2) {
    console.log("url AND path to css required.")
}

var url = args[0];
var css = args[1];
var outputPath = args[2] ? args[2] : "critical.css";
var viewportWidth = args[3] ? args[3] : 1300;
var viewportHeight = args[4] ? args[4] : 1000;
var forceInclude = args[5] ? JSON.parse(args[5]) : [];
var forceExclude = args[6] ? JSON.parse(args[6]) : [];
var generationTimeout = args[7] ? args[7] : 30000;
var renderWaitTime = args[8] ? args[8] : 100;
var keepLargerMediaQueries = args[9] ? args[9] : false;
var enableJSRequests = args[10] ? args[10] : false;

// var imageName = outputPath.replace('.css', '');


const browserPromise = puppeteer.launch({
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-dev-shm-usage',
      '--window-size=1920,1200',
    ],
    defaultViewport: {
      width: 1920,
      height: 1200,
    },
  });

penthouse({
    url,
    css,
    viewportWidth: viewportWidth,
    viewportHeight: viewportHeight,
    forceInclude: forceInclude,
    forceExclude: forceExclude,
    timeout: generationTimeout,
    renderWaitTime: renderWaitTime,
    keepLargerMediaQueries: keepLargerMediaQueries,
    blockJSRequests: !enableJSRequests,
    maxEmbeddedBase64Length: 3000,
    allowedResponseCode: 200,
    puppeteer: {
        getBrowser: () => browserPromise,
      },
    // screenshots: {
    //     basePath: imageName, // absolute or relative; excluding file extension
    //     type: 'jpeg', // jpeg or png, png default
    //     quality: 20 // only applies for jpeg type
    // }
})
.then(criticalCss => {
    // use the critical css
    fs.writeFileSync(outputPath, criticalCss);
}).catch(error => {
    console.log(error.message);
})