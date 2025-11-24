Place your background video files here.
Current background videos in use (source order = preferred → fallback):
 1. UB-Homepage-Video-w-text.webm (VP9) – smaller, better compression
 2. UB-Homepage-Video-w-text.mp4 (H.264) – compatibility fallback

Compression tips:
- Use HandBrake or ffmpeg: ffmpeg -i UB-Homepage-Video-w-text.mp4 -vf scale=1280:-1 -c:v libx264 -profile:v high -crf 24 -preset medium -an UB-Homepage-Video-w-text-optimized.mp4
 - WebM example: ffmpeg -i UB-Homepage-Video-w-text.mp4 -vf scale=1280:-1 -c:v libvpx-vp9 -b:v 0 -crf 32 -an UB-Homepage-Video-w-text.webm
 - Keep duration short (10–20s) and loop seamlessly.
 - Avoid audio track (we mute anyway).

Fallback poster image referenced: assets/img/landing-bg.jpg (replace with a frame export if mismatched)

Performance & Accessibility notes:
- WebM tried first for supporting browsers, MP4 fallback covers Safari/legacy.
- Reduced motion users still get static gradient (no poster used).
- Overlay + brightness tuned for readability; adjust in assets/css/style.css.
- Keep both files under ~4MB each to avoid slow first paint on slower networks.
Ensure both files exist; missing WebM will just fall back silently to MP4.
