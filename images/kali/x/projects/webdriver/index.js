const fs = require('fs');
const readline = require('readline');
const { Cluster } = require('puppeteer-cluster');

function readLines(filename) {
    return new Promise((resolve) => {
        let lines = [];
        const readInterface = readline.createInterface({
            input: fs.createReadStream(filename),
        });
        readInterface.on('line', line => lines.push(line));
        readInterface.on('close', data => resolve(lines));
    });
}
function writeLine(filename, data) {
    return new Promise((resolve) => {
        fs.writeFile(filename, data, () => {});
        resolve();
    });
}

let cluster = null;

(async () => {
    let ipsFile = '/home/pilot114/projects/my_docker/images/kali/x/inet/BY/cidr_ok';
    let screenshotPath = '/home/pilot114/projects/my_docker/images/kali/x/inet/BY/pages/';
    let progressFile ='/home/pilot114/projects/my_docker/images/kali/webdriver/progress';

    let ips = await readLines(ipsFile);
    let lastIp = (await readLines(progressFile))[0];

    cluster = await Cluster.launch({
        concurrency: Cluster.CONCURRENCY_BROWSER,
        maxConcurrency: 10,
        monitor: false
    });

    await cluster.task(async ({ page, data: ip }) => {
        try {
            await page.setUserAgent("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36");
            await page.goto('http://' + ip, { waitUntil: 'domcontentloaded' });
            await page.screenshot({path: screenshotPath + ip + '.png'});
            console.log('done:', ip, "\n");
            await writeLine(progressFile, ip);
        } catch(error) {
            console.log('error:', error.message, ip, "\n");
        }
    });

    let skip = true;
    for (let ip of ips) {
        // продолжить скан с определенного ip
        if(ip === lastIp) skip = false;
        if (skip) continue;

        await cluster.queue(ip);
    }
    await cluster.idle();
    await cluster.close();

    process.exit();
})();

process.on('exit', async () => {
    console.log('shutdown...');
    await cluster.idle();
    await cluster.close();
    process.exit();
});
