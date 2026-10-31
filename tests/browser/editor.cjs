'use strict';

/**
 * OPTIONAL destructive UI integration test. Use ONLY a disposable Bagisto database and storage.
 * Prerequisites: playwright-core + Chromium; an active, initialized AponBasket test channel;
 * English admin UI; a test admin account; and a local sample image. Never use production.
 *
 * Required environment: APON_TEST_DISPOSABLE=yes, APON_TEST_URL, APON_TEST_EMAIL,
 * APON_TEST_PASSWORD. Optional: APON_TEST_CHANNEL (default: default), APON_TEST_LOCALE
 * (default: en), APON_TEST_IMAGE, APON_TEST_CHROMIUM, APON_TEST_BROWSER_ARGS,
 * APON_TEST_HEADER_NAME and APON_TEST_HEADER_VALUE for a protected test proxy.
 */

const assert = require('node:assert/strict');
const path = require('node:path');

if (process.env.APON_TEST_DISPOSABLE !== 'yes') {
    throw new Error('This test publishes content and uploads an image. Set APON_TEST_DISPOSABLE=yes only for an isolated disposable Bagisto.');
}

for (const name of ['APON_TEST_URL', 'APON_TEST_EMAIL', 'APON_TEST_PASSWORD']) {
    if (! process.env[name]) throw new Error(`Missing ${name}`);
}

const { chromium } = require('playwright-core');
const base = process.env.APON_TEST_URL.replace(/\/$/, '');
const channel = process.env.APON_TEST_CHANNEL || 'default';
const locale = process.env.APON_TEST_LOCALE || 'en';
const image = process.env.APON_TEST_IMAGE || path.resolve(__dirname, '../../public/themes/shop/aponbasket/images/turmeric-promo.jpg');
const extraHTTPHeaders = {};

if (process.env.APON_TEST_HEADER_NAME && process.env.APON_TEST_HEADER_VALUE) {
    extraHTTPHeaders[process.env.APON_TEST_HEADER_NAME] = process.env.APON_TEST_HEADER_VALUE;
}

(async () => {
    const browser = await chromium.launch({
        headless: true,
        ...(process.env.APON_TEST_CHROMIUM ? { executablePath: process.env.APON_TEST_CHROMIUM } : {}),
        args: (process.env.APON_TEST_BROWSER_ARGS || '').split(' ').filter(Boolean),
    });

    try {
        const context = await browser.newContext({ viewport: { width: 1500, height: 950 }, extraHTTPHeaders });
        const page = await context.newPage();
        const pageErrors = [];
        page.on('pageerror', error => pageErrors.push(error.message));

        await page.goto(`${base}/admin/login`, { waitUntil: 'networkidle' });
        await page.locator('input[name=email]').fill(process.env.APON_TEST_EMAIL);
        await page.locator('input[name=password]').fill(process.env.APON_TEST_PASSWORD);
        await page.getByRole('button', { name: 'Sign In' }).click();
        await page.waitForURL(url => ! url.pathname.includes('/login'), { timeout: 20000 });

        const query = new URLSearchParams({ channel, locale });
        await page.goto(`${base}/admin/appearance/themes/aponbasket/sections?${query}`, { waitUntil: 'networkidle' });
        await page.getByRole('button', { name: /হিরো ব্যানার/ }).first().click();
        const field = page.getByText('শিরোনামের প্রথম লাইন', { exact: true }).locator('..').locator('input');
        await field.waitFor();
        await page.evaluate(() => document.fonts.ready);
        const fontLoaded = await page.evaluate(() => [...document.fonts].some(face => face.family === 'AponBasket Editor Bengali' && face.status === 'loaded'));
        assert(fontLoaded, 'The Bengali editor font did not load');

        const original = await field.inputValue();
        assert(original, 'The test channel must have an initialized hero title');
        const updated = `পরীক্ষামূলক শিরোনাম ${Date.now()}`;
        const draftResponse = page.waitForResponse(response => response.request().method() === 'POST' && response.url().includes('/draft') && response.status() === 200);
        await field.fill(updated);
        await draftResponse;
        await page.waitForFunction(text => [...document.querySelectorAll('iframe')].some(frame => frame.contentDocument?.querySelector('.ab-hero h1')?.textContent.includes(text)), updated, { timeout: 25000 });

        const storefront = await context.newPage();
        await storefront.goto(`${base}/`, { waitUntil: 'networkidle' });
        assert((await storefront.locator('.ab-hero h1').innerText()).includes(original), 'Draft leaked onto the live storefront');

        const mediaResponse = page.waitForResponse(response => response.request().method() === 'POST' && response.url().includes('/media') && response.status() === 200);
        await page.getByText('হিরো ছবি', { exact: true }).locator('..').locator('input[type=file]').setInputFiles(image);
        const media = await (await mediaResponse).json();
        assert(media.path, 'Image upload did not return a path');
        await page.waitForFunction(value => [...document.querySelectorAll('iframe')].some(frame => frame.contentDocument?.querySelector('.ab-hero__image-frame img')?.getAttribute('src')?.includes(value)), media.path, { timeout: 25000 });

        await page.locator('.icon-cross:visible').last().click();
        const publishResponse = page.waitForResponse(response => response.request().method() === 'POST' && response.url().includes('/publish') && response.status() === 200);
        await page.getByRole('button', { name: /^Publish/ }).click();
        await publishResponse;
        await storefront.reload({ waitUntil: 'networkidle' });
        assert((await storefront.locator('.ab-hero h1').innerText()).includes(updated), 'Published title did not reach the storefront');
        const publishedImage = storefront.locator('.ab-hero__image-frame img');
        assert((await publishedImage.getAttribute('src')).includes(media.path), 'Published image did not reach the storefront');
        assert(await publishedImage.evaluate(element => element.complete && element.naturalWidth > 0), 'Published image did not load');
        assert.deepEqual(pageErrors, [], 'The editor raised browser errors');

        console.log('ADMIN BROWSER TEST PASSED: Bengali font, draft, preview, live isolation, image upload and publish.');
        await context.close();
    } finally {
        await browser.close();
    }
})().catch(error => {
    console.error(error);
    process.exitCode = 1;
});
