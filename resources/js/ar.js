// Public AR viewer — runs entirely in the doctor's browser.
// Self-hosted MindAR (image tracking) + three.js. No external services are called
// (media is fetched from presigned S3 URLs supplied by the page).
import { MindARThree } from 'mind-ar/dist/mindar-image-three.prod.js';
import { Mesh, MeshBasicMaterial, PlaneGeometry, VideoTexture } from 'three';

const root = document.getElementById('ar-root');
const startScreen = document.getElementById('ar-start');
const startButton = document.getElementById('ar-start-button');
const statusEl = document.getElementById('ar-status');
const errorScreen = document.getElementById('ar-error');
const errorText = document.getElementById('ar-error-text');

const config = {
    mindSrc: root.dataset.mind,
    videoSrc: root.dataset.video,
    loop: root.dataset.playMode !== 'once',
    markerAspect: parseFloat(root.dataset.markerAspect) || 1,
};

function fail(message) {
    if (errorText) errorText.textContent = message;
    startScreen?.classList.add('hidden');
    errorScreen?.classList.remove('hidden');
}

// The camera (getUserMedia) is only available in a secure context: HTTPS, or
// localhost. Plain http://<ip> disables it entirely.
if (!window.isSecureContext) {
    fail('AR needs a secure (HTTPS) connection. This page is open over plain HTTP, so the browser blocks the camera. Open it over HTTPS.');
} else if (!navigator.mediaDevices?.getUserMedia) {
    fail('Your browser can’t open the camera here. Please open this page in Safari or Chrome.');
}

// The hidden <video> that gets painted onto the marker. crossOrigin is required
// so the cross-origin (S3) video can be used as a WebGL texture.
const video = document.createElement('video');
video.src = config.videoSrc;
video.loop = config.loop;
video.playsInline = true;
video.setAttribute('playsinline', '');
video.setAttribute('webkit-playsinline', '');
video.crossOrigin = 'anonymous';
video.preload = 'auto';

let mindarThree = null;
let started = false;

async function start() {
    if (started) return;
    started = true;
    startScreen?.classList.add('hidden');

    // "Unlock" the video inside the user gesture so later play() calls work on iOS.
    try {
        await video.play();
        video.pause();
        video.currentTime = 0;
    } catch (e) {
        // Ignore — playback resumes on target detection.
    }

    try {
        mindarThree = new MindARThree({
            container: root,
            imageTargetSrc: config.mindSrc,
            uiScanning: 'no',
            uiLoading: 'no',
            uiError: 'no',
            // Stabilise the overlay. MindAR's defaults (filterMinCF 0.001,
            // filterBeta 1000) are tuned for responsiveness and let the pose
            // jitter "shake". Heavier smoothing keeps a near-static video locked.
            filterMinCF: 0.0001,
            filterBeta: 1,
        });

        const { renderer, scene, camera } = mindarThree;
        const anchor = mindarThree.addAnchor(0);

        const texture = new VideoTexture(video);
        // Width = 1 (MindAR normalises the marker width to 1); height = marker
        // aspect, so the plane exactly covers the marker. The video fills it.
        const geometry = new PlaneGeometry(1, config.markerAspect);
        const material = new MeshBasicMaterial({ map: texture, transparent: true });
        const plane = new Mesh(geometry, material);
        anchor.group.add(plane);

        anchor.onTargetFound = () => {
            statusEl?.classList.add('hidden');
            video.play().catch(() => {});
        };
        anchor.onTargetLost = () => {
            statusEl?.classList.remove('hidden');
            video.pause();
        };

        await mindarThree.start();
        renderer.setAnimationLoop(() => renderer.render(scene, camera));
        statusEl?.classList.remove('hidden');

        // Nudge MindAR's resize handler so the camera fills the viewport.
        setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
    } catch (e) {
        fail('Couldn’t start the camera. Please allow camera access and reload.');
    }
}

startButton?.addEventListener('click', start);
