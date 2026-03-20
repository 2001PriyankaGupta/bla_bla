const fs = require('fs');
const path = require('path');

const configPath = 'd:/react-native/Full-bla-bla-new/backend/config/app.php';
let config = fs.readFileSync(configPath, 'utf8');
config = config.replace(/'timezone'\s*=>\s*'[^']+',/, "'timezone' => 'Asia/Kolkata',");
fs.writeFileSync(configPath, config);
console.log('Timezone updated in app.php');

function processDir(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            processDir(fullPath);
        } else if (fullPath.endsWith('.blade.php')) {
            let content = fs.readFileSync(fullPath, 'utf8');
            let newContent = content.replace(/->format\('([^']+)'\)/g, (match, formatString) => {
                if (formatString === 'Y-m-d\\TH:i') return match; // exclude datetime-local inputs

                // If it contains hour representations:
                if (/[HhgGi]/.test(formatString)) {
                    if (formatString === 'h:i A') return match; // just the time, we can leave this
                    return "->format('d/m/Y h:i A')";
                } else {
                    return "->format('d/m/Y')";
                }
            });
            if (content !== newContent) {
                fs.writeFileSync(fullPath, newContent);
                console.log('Fixed dates in:', fullPath);
            }
        }
    }
}

processDir('d:/react-native/Full-bla-bla-new/backend/resources/views');
