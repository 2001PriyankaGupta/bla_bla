const fs = require('fs');
const p = 'd:/react-native/Full-bla-bla-new/backend/config/jwt.php';
if (fs.existsSync(p)) {
    let c = fs.readFileSync(p, 'utf8');
    c = c.replace(/'ttl' => env\('JWT_TTL', 60\)/, "'ttl' => env('JWT_TTL', 43200)");
    fs.writeFileSync(p, c);
    console.log('TTL updated to 1 month');
}
