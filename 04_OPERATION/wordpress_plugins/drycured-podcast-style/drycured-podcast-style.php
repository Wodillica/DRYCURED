<?php
/**
 * Plugin Name: Drycured Podcast Style
 * Description: Stilizacija Drycured podcast stranica.
 * Version: 1.0.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_head', function () {
    if (!is_page()) {
        return;
    }
    ?>
    <style id="drycured-podcast-style">
      .dc-podcast-shell {
        width: min(1120px, calc(100vw - 40px));
        margin-left: 50%;
        transform: translateX(-50%);
        padding: 34px 0 72px;
        color: #1f2530;
      }

      .dc-podcast-hero {
        position: relative;
        overflow: hidden;
        padding: 42px 46px;
        border-radius: 28px;
        background:
          radial-gradient(circle at 90% 10%, rgba(212,154,58,.18), transparent 30%),
          linear-gradient(135deg, #1f2530 0%, #3b3024 54%, #8b6f47 100%);
        color: #fffaf0;
        box-shadow: 0 22px 54px rgba(31,37,48,.18);
      }

      .dc-podcast-kicker {
        margin-bottom: 12px;
        font-size: 12px;
        line-height: 1.2;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: #f2c879;
        font-weight: 700;
      }

      .dc-podcast-hero h1 {
        max-width: 760px;
        margin: 0 0 14px;
        font-size: clamp(28px, 3vw, 42px) !important;
        line-height: 1.12 !important;
        letter-spacing: -0.02em !important;
        font-weight: 700 !important;
        color: #fffaf0 !important;
      }

      .dc-podcast-lead {
        max-width: 760px;
        margin: 0 0 28px;
        font-size: 18px !important;
        line-height: 1.65 !important;
        color: rgba(255,250,240,.88) !important;
      }

      .dc-audio-card {
        max-width: 820px;
        padding: 18px 20px 16px;
        border-radius: 18px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        backdrop-filter: blur(10px);
      }

      .dc-audio-meta {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 10px;
        font-size: 13px;
        color: rgba(255,255,255,.76);
      }

      .dc-audio-meta strong {
        color: #f2c879;
        font-weight: 700;
      }

      .dc-audio-card .wp-audio-shortcode,
      .dc-audio-card audio {
        width: 100% !important;
      }

      .dc-podcast-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(280px, .8fr);
        gap: 22px;
        margin-top: 24px;
      }

      .dc-podcast-card {
        padding: 28px 30px;
        border-radius: 24px;
        background: #fffaf0;
        border: 1px solid rgba(139,111,71,.16);
        box-shadow: 0 14px 34px rgba(31,37,48,.07);
      }

      .dc-podcast-card h2 {
        margin: 0 0 14px;
        font-size: 23px !important;
        line-height: 1.28 !important;
        letter-spacing: -0.01em !important;
        font-weight: 650 !important;
        color: #1f2530 !important;
      }

      .dc-podcast-card h3 {
        margin: 0 0 8px;
        font-size: 17px !important;
        line-height: 1.35 !important;
        font-weight: 650 !important;
        color: #2f2a24 !important;
      }

      .dc-podcast-card p,
      .dc-podcast-card li {
        font-size: 16px !important;
        line-height: 1.72 !important;
        color: #4c535d !important;
      }

      .dc-podcast-card p:last-child {
        margin-bottom: 0;
      }

      .dc-podcast-side ul {
        margin: 0;
        padding-left: 18px;
      }

      .dc-podcast-points {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
      }

      .dc-podcast-points > div {
        padding: 18px 18px 16px;
        border-radius: 18px;
        background: rgba(255,255,255,.62);
        border: 1px solid rgba(139,111,71,.14);
      }

      .dc-podcast-points p {
        margin: 0;
        font-size: 14px !important;
        line-height: 1.6 !important;
      }

      .dc-podcast-related {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin-top: 22px;
      }

      .dc-podcast-link-card {
        display: block;
        padding: 22px 24px;
        border-radius: 22px;
        background: #ffffff;
        border: 1px solid rgba(139,111,71,.16);
        box-shadow: 0 10px 26px rgba(31,37,48,.06);
        text-decoration: none !important;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
      }

      .dc-podcast-link-card:hover {
        transform: translateY(-2px);
        border-color: rgba(211,154,58,.42);
        box-shadow: 0 18px 38px rgba(31,37,48,.10);
      }

      .dc-podcast-link-card span {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        letter-spacing: .13em;
        text-transform: uppercase;
        color: #d39a3a;
        font-weight: 800;
      }

      .dc-podcast-link-card strong {
        display: block;
        margin-bottom: 8px;
        font-size: 18px;
        line-height: 1.25;
        color: #1f2530;
      }

      .dc-podcast-link-card em {
        display: block;
        font-style: normal;
        font-size: 14px;
        line-height: 1.55;
        color: #68707a;
      }

      @media (max-width: 900px) {
        .dc-podcast-shell {
          width: min(100%, calc(100vw - 28px));
          padding-top: 22px;
        }

        .dc-podcast-hero {
          padding: 32px 26px;
          border-radius: 22px;
        }

        .dc-podcast-grid,
        .dc-podcast-points,
        .dc-podcast-related {
          grid-template-columns: 1fr;
        }
      }
    </style>
    <?php
}, 10030);
