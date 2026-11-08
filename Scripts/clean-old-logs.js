import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const scriptFolder = path.dirname(fileURLToPath(import.meta.url));
const logsFolder = path.join(scriptFolder, '..', 'storage', 'logs');
const maxAgeInDays = 14;
const oldestAllowed = Date.now() - maxAgeInDays * 24 * 60 * 60 * 1000;

let deletedCount = 0;

for (const fileName of fs.readdirSync(logsFolder)) {
    if (!fileName.endsWith('.log')) {
        continue;
    }

    const filePath = path.join(logsFolder, fileName);

    if (fs.statSync(filePath).mtimeMs < oldestAllowed) {
        fs.unlinkSync(filePath);
        deletedCount++;
    }
}

console.log(`Deleted ${deletedCount} log file(s) older than ${maxAgeInDays} days.`);