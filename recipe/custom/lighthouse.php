<?php

namespace Deployer;

class LighthouseConfig
{
    private $targetUrl = '';
    private $basicAuthToken = '';
    private $slackAuthToken = '';
    private $slackChannels = '';
    private $projectSlug = '';

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(string $targetUrl): self
    {
        $this->targetUrl = $targetUrl;
        return $this;
    }

    public function getBasicAuthToken(): string
    {
        return $this->basicAuthToken;
    }

    public function setBasicAuthToken(string $basicAuthToken): self
    {
        $this->basicAuthToken = $basicAuthToken;
        return $this;
    }

    public function getSlackAuthToken(): string
    {
        return $this->slackAuthToken;
    }

    public function setSlackAuthToken(string $slackAuthToken): self
    {
        $this->slackAuthToken = $slackAuthToken;
        return $this;
    }

    public function getSlackChannels(): string
    {
        return $this->slackChannels;
    }

    public function setSlackChannels(string $slackChannels): self
    {
        $this->slackChannels = $slackChannels;
        return $this;
    }

    public function getProjectSlug(): string
    {
        return $this->projectSlug;
    }

    public function setProjectSlug(string $projectSlug): self
    {
        $this->projectSlug = $projectSlug;
        return $this;
    }

    public function validate(): bool
    {
        return $this->slackAuthToken !== '' 
            && $this->slackChannels !== '' 
            && $this->targetUrl !== '' 
            && $this->projectSlug !== '';
    }
}


desc('Generating Lighthouse reports and sending them via Slack');
task('lighthouse:generate', function () {
    $lighthouseConfig = get('lighthouse');
    if (!$lighthouseConfig instanceof LighthouseConfig || !$lighthouseConfig->validate()) {
        return;
    }

    $extraHeaders = $lighthouseConfig->getBasicAuthToken()
        ? "--extra-headers \"{\\\"authorization\\\": \\\"Basic {$lighthouseConfig->getBasicAuthToken()}\\\"}\""
        : '';
    $curlBasicAuth = $lighthouseConfig->getBasicAuthToken()
        ? "-H \"Authorization: Basic {$lighthouseConfig->getBasicAuthToken()}\""
        : '';

    // Make sure the caches are warm
    runLocally("curl {$lighthouseConfig->getTargetUrl()} {$curlBasicAuth} > /dev/null");

    // Generate results for mobile and push to Slack
    runLocally("lighthouse {$lighthouseConfig->getTargetUrl()} --quiet --no-enable-error-reporting \
    --chrome-flags=\"--headless --no-sandbox\" --form-factor=mobile \
    --output-path={$lighthouseConfig->getProjectSlug()}-mobile.html {$extraHeaders}");
    runLocally("curl -F file=@{$lighthouseConfig->getProjectSlug()}-mobile.html \
    -F \"initial_comment=Here are the mobile :iphone: Lighthouse results for your latest deployment :rocket:\" \
    -F channels={$lighthouseConfig->getSlackChannels()} \
    -H \"Authorization: Bearer {$lighthouseConfig->getSlackAuthToken()}\" \
    https://slack.com/api/files.upload");

    // Generate results for desktop and push to Slack
    runLocally("lighthouse {$lighthouseConfig->getTargetUrl()} --quiet --no-enable-error-reporting \
    --chrome-flags=\"--headless --no-sandbox\" --form-factor=desktop  --screenEmulation.disabled \
    --output-path={$lighthouseConfig->getProjectSlug()}-desktop.html {$extraHeaders}");
    runLocally("curl -F file=@{$lighthouseConfig->getProjectSlug()}-desktop.html \
    -F \"initial_comment=Here are the desktop :computer: Lighthouse results for your latest deployment :rocket:\" \
    -F channels={$lighthouseConfig->getSlackChannels()} \
    -H \"Authorization: Bearer {$lighthouseConfig->getSlackAuthToken()}\" \
    https://slack.com/api/files.upload");
});

