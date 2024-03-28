import svelte from 'rollup-plugin-svelte';
import resolve from '@rollup/plugin-node-resolve';
import image from '@rollup/plugin-image';
import commonjs from '@rollup/plugin-commonjs';
import { readFileSync } from 'fs';

const { API_URL } = process.env;


const emailSVG = Buffer.from(readFileSync('./email.svg', 'utf-8')).toString('base64');
const closeSVG = Buffer.from(readFileSync('./close.svg', 'utf-8')).toString('base64');

export default {
	input: 'embed.js',
	output: {
		format: 'iife',
		file: 'newsletter.js',
        sourcemap: false,
		banner: `
            const API_URL = "${API_URL}";
            const SVG_EMAIL_DATA = \`${emailSVG}\`;
            const SVG_CLOSE_DATA = \`${closeSVG}\`;
        `,
	},
	plugins: [
		svelte({ emitCss: false, }),
		resolve({ browser: true, dedupe: ['svelte'] }),
        commonjs(),
        image(),
	],
}
