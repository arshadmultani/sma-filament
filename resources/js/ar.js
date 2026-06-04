// Public AR viewer — runs entirely in the doctor's browser.
// Self-hosted MindAR (image tracking) + three.js. No external services are called
// (media is fetched from presigned S3 URLs supplied by the page).
import { MindARThree } from 'mind-ar/dist/mindar-image-three.prod.js';
import {
    Group,
    Matrix4,
    Mesh,
    MeshBasicMaterial,
    PlaneGeometry,
    Quaternion,
    Vector3,
    VideoTexture,
} from 'three';

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
            // Keep MindAR's own filter responsive (near defaults). We do the
            // stabilising ourselves below, in the render loop, so the two filters
            // don't fight (over-smoothing MindAR makes the video drift/float).
            filterMinCF: 0.001,
            filterBeta: 1000,
        });

        const { renderer, scene, camera } = mindarThree;
        const anchor = mindarThree.addAnchor(0);

        const texture = new VideoTexture(video);
        // Width = 1 (MindAR normalises the marker width to 1); height = marker
        // aspect, so the plane exactly covers the marker. The video fills it.
        const geometry = new PlaneGeometry(1, config.markerAspect);
        const material = new MeshBasicMaterial({ map: texture, transparent: true });
        const plane = new Mesh(geometry, material);

        // The plane does NOT live on MindAR's anchor. Instead it lives on our own
        // group, into which we copy the marker pose each frame with exponential
        // smoothing. This adds "stickiness": the raw pose MindAR solves jitters
        // frame-to-frame, so following it 1:1 makes the video shake. We chase the
        // target pose a fraction per frame, killing the shake while staying locked.
        const stage = new Group();
        stage.matrixAutoUpdate = false;
        stage.visible = false;
        stage.add(plane);
        scene.add(stage);

        // Lower = stickier/heavier smoothing (and slightly more lag). 0..1.
        const SMOOTH = 0.12;
        // Deadzone: if the freshly solved pose is within these thresholds of the
        // current one, treat it as "no real movement" and hold the last pose. This
        // is what kills the residual shake when camera + marker are both still —
        // MindAR keeps emitting micro-jittered poses, and we refuse to chase them.
        const POS_DEAD = 0.0025; // ~0.25% of marker width
        const ROT_DEAD = 0.004; // radians (~0.23°)

        let visible = false;
        let primed = false; // snap to the first solved pose, smooth after that
        const tPos = new Vector3();
        const tQuat = new Quaternion();
        const tScale = new Vector3();
        const pos = new Vector3();
        const quat = new Quaternion();
        const scale = new Vector3();
        const mat = new Matrix4();

        anchor.onTargetFound = () => {
            statusEl?.classList.add('hidden');
            visible = true;
            primed = false;
            video.play().catch(() => {});
        };
        anchor.onTargetLost = () => {
            statusEl?.classList.remove('hidden');
            visible = false;
            video.pause();
        };

        await mindarThree.start();
        renderer.setAnimationLoop(() => {
            if (visible) {
                anchor.group.matrix.decompose(tPos, tQuat, tScale);
                if (!primed) {
                    pos.copy(tPos);
                    quat.copy(tQuat);
                    scale.copy(tScale);
                    primed = true;
                } else {
                    // Hold still inside the deadzone, otherwise ease toward the
                    // new pose. Position and rotation are gated independently.
                    if (pos.distanceTo(tPos) > POS_DEAD) {
                        pos.lerp(tPos, SMOOTH);
                        scale.lerp(tScale, SMOOTH);
                    }
                    if (quat.angleTo(tQuat) > ROT_DEAD) {
                        quat.slerp(tQuat, SMOOTH);
                    }
                }
                stage.matrix.compose(pos, quat, scale);
                stage.visible = true;
            } else {
                stage.visible = false;
            }
            renderer.render(scene, camera);
        });
        statusEl?.classList.remove('hidden');

        // Nudge MindAR's resize handler so the camera fills the viewport.
        setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
    } catch (e) {
        fail('Couldn’t start the camera. Please allow camera access and reload.');
    }
}

startButton?.addEventListener('click', start);
