<?php

namespace Tests\Feature\Security;

use Tests\TestCase;

class XssProtectionTest extends TestCase
{
    /**
     * Test that HTMLPurifier strips <script> tags.
     */
    public function test_script_tag_is_stripped(): void
    {
        $malicious = '<p>Hello</p><script>alert(1)</script><p>World</p>';
        $result = clean($malicious);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('alert(1)', $result);
        $this->assertStringContainsString('<p>Hello</p>', $result);
        $this->assertStringContainsString('<p>World</p>', $result);
    }

    /**
     * Test that onerror event handler is stripped from img tags.
     */
    public function test_onerror_attribute_is_stripped(): void
    {
        $malicious = '<img src="x" onerror="alert(1)">';
        $result = clean($malicious);

        $this->assertStringNotContainsString('onerror', $result);
        $this->assertStringNotContainsString('alert(1)', $result);
    }

    /**
     * Test that javascript: protocol in href is stripped.
     */
    public function test_javascript_protocol_is_stripped(): void
    {
        $malicious = '<a href="javascript:alert(1)">click me</a>';
        $result = clean($malicious);

        $this->assertStringNotContainsString('javascript:', $result);
    }

    /**
     * Test that valid HTML formatting is preserved.
     */
    public function test_valid_html_is_preserved(): void
    {
        $safe = '<strong>bold</strong> <em>italic</em> <u>underline</u>';
        $result = clean($safe);

        $this->assertStringContainsString('<strong>bold</strong>', $result);
        $this->assertStringContainsString('<em>italic</em>', $result);
        $this->assertStringContainsString('<u>underline</u>', $result);
    }

    /**
     * Test that academic content tags are preserved.
     */
    public function test_academic_html_tags_preserved(): void
    {
        $academic = '<h2>Introduction</h2><p>This study<sup>1</sup> examines...</p>'
            . '<blockquote>A key finding</blockquote>'
            . '<ul><li>Item 1</li><li>Item 2</li></ul>'
            . '<table><thead><tr><th>Col</th></tr></thead><tbody><tr><td>Data</td></tr></tbody></table>';

        $result = clean($academic);

        $this->assertStringContainsString('<h2>', $result);
        $this->assertStringContainsString('<sup>', $result);
        $this->assertStringContainsString('<blockquote>', $result);
        $this->assertStringContainsString('<ul>', $result);
        $this->assertStringContainsString('<table>', $result);
    }

    /**
     * Test that iframe/embed/object tags are stripped.
     */
    public function test_dangerous_embed_tags_stripped(): void
    {
        $malicious = '<iframe src="https://evil.com"></iframe>'
            . '<embed src="evil.swf">'
            . '<object data="evil.swf"></object>'
            . '<form action="https://evil.com"><input type="submit"></form>';

        $result = clean($malicious);

        $this->assertStringNotContainsString('<iframe', $result);
        $this->assertStringNotContainsString('<embed', $result);
        $this->assertStringNotContainsString('<object', $result);
        $this->assertStringNotContainsString('<form', $result);
        $this->assertStringNotContainsString('<input', $result);
    }

    /**
     * Test that style tag (inline CSS injection) is stripped.
     */
    public function test_style_tag_is_stripped(): void
    {
        $malicious = '<style>body{background:url("javascript:alert(1)")}</style><p>Content</p>';
        $result = clean($malicious);

        $this->assertStringNotContainsString('<style>', $result);
        $this->assertStringContainsString('<p>Content</p>', $result);
    }

    /**
     * Test that data: URI in img src is blocked.
     */
    public function test_data_uri_blocked(): void
    {
        $malicious = '<img src="data:text/html,<script>alert(1)</script>">';
        $result = clean($malicious);

        $this->assertStringNotContainsString('data:', $result);
    }

    /**
     * Test clean() handles null/empty input gracefully.
     */
    public function test_handles_null_and_empty(): void
    {
        $this->assertEquals('', clean(''));
        $this->assertEquals('', clean(null));
    }
}
