const fs = require('fs');
const path = require('path');

const modelsDir = 'd:/react-native/Full-bla-bla-new/backend/app/Models';

const serializeFunc = `
    protected function serializeDate(\\DateTimeInterface $date)
    {
        return $date->setTimezone(new \\DateTimeZone(config('app.timezone', 'Asia/Kolkata')))->format('Y-m-d H:i:s');
    }
`;

function processDir(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            processDir(fullPath);
        } else if (fullPath.endsWith('.php')) {
            let content = fs.readFileSync(fullPath, 'utf8');
            if (content.includes('serializeDate')) continue; // skip if already defined

            // Add at the end of class before the last closing brace
            // Matches class { ... } and inserts before the last }
            const lastBraceIndex = content.lastIndexOf('}');
            if (lastBraceIndex !== -1) {
                content = content.substring(0, lastBraceIndex) + serializeFunc + '\n' + content.substring(lastBraceIndex);
                fs.writeFileSync(fullPath, content);
                console.log('Added serializeDate to:', fullPath);
            }
        }
    }
}

processDir(modelsDir);
