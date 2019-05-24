/*
node extractCSS.js Coverage-20190519T125859.json
*/
const fs = require('fs');

let final_css_bytes = '';
let total_bytes = 0;
let used_bytes = 0;

const filename = process.argv[2]
const output = './final_css.css'

if(!filename) {
  console.error('Missing filename to get coverage information from');
  process.exit();
}

const file_coverage = fs.readFileSync(filename);

const css_coverage = JSON.parse(file_coverage);

for (const entry of css_coverage) {
  if (!entry.url.split("?")[0].endsWith('.css')) continue;
  console.log(entry.url)
  final_css_bytes += '# ' + entry.url + '\n\n'
  total_bytes += entry.text.length;
  for (const range of entry.ranges) {
    used_bytes += range.end - range.start - 1;
    final_css_bytes += entry.text.slice(range.start, range.end) + '\n';
  }
  final_css_bytes += '\n\n'
}

fs.writeFile(output, final_css_bytes, error => {
  if (error) {
    console.log('Error creating file:', error);
    return
  }
  console.log(output, 'file saved');
});
