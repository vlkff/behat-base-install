<?php

trait TokenFaker {

    public function checkForTokens($arg)
    {
        $arg =  str_replace($this->replaceAbleTokens(), $this->loadTokensValues(), $arg);

        return $arg;
    }

    protected function replaceAbleTokens()
    {
        return array_merge($this->getManualTokensKeys(), $this->getEnvTokensKeys());
    }

    protected function getManualTokensKeys()
    {
        return [
            'FAKER_EMAIL',
            'FAKER_UUID',
            'FAKER_USERNAME',
            'FAKER_URL',
            'FAKER_PARAGRAPH',
            'REFRESH_TOKEN_FROM_STATE'];
    }

    protected function loadTokensValues()
    {
        return array_merge($this->getManualTokenValues(), $this->getEnvTokensValues());
    }

    protected function getManualTokenValues()
    {
        return [
            $this->faker->email,
            $this->faker->uuid,
            $this->faker->word,
            $this->faker->url,
            $this->faker->paragraph(3),
            $this->refresh_token
        ];
    }

    protected function getEnvTokensValues()
    {
        return array_values($_ENV);
    }

    protected function getEnvTokensKeys()
    {
        return array_keys($_ENV);
    }

}