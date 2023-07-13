# Admin panel documentation

If you see this message, it means you need to configure the YouTube package.

![Modal](https://github.com/EscolaLMS/Youtube/assets/59456825/b08ad70f-5617-4830-8c49-e9c17201fa4e)

To configure the YouTube package, follow these steps:
1. Go to Settings and then select the "Services" tab. 
2. Set the selected variables.

![Settings](https://github.com/EscolaLMS/Youtube/assets/59456825/a9563365-e49b-4734-a4ef-0466dca79ff5)


You can find the necessary values on your Google Developers Console account.

The following steps are outside the LMS and apply to your YouTube/Google account:
1. Follow the steps in the documentation provided by Google: [YouTube Data API Overview](https://developers.google.com/youtube/v3/getting-started).
2. Remember to select the "Web application" type.
3. The API Key, Client ID, and Client secret generated during this process will be needed in the LMS settings.

Additionally, you will need to enable streaming on your YouTube channel:

1. Select the "Broadcast live" option. If your account does not have a created channel, you will be prompted to create one.
2. Choose "Transmit Live"
3. Click send request and enter phone number
4. The streaming capability will be activated within 24 hours.
