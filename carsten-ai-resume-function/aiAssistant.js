const axios = require('axios');

module.exports = async function (context, req) {
  context.log('Processing request for AI Assistant Resume function.');

  // Ensure the request body has the expected structure
  if (!req.body || !req.body.prompt) {
    context.res = {
      status: 400,
      body: "Please provide a 'prompt' in the request body",
    };
    return;
  }

  const prompt = req.body.prompt;

  // OpenAI API call
  try {
    const response = await axios.post(
      'https://api.openai.com/v1/completions',
      {
        model: 'gpt-4',
        prompt: prompt,
        max_tokens: 150,
        temperature: 0.7,
      },
      {
        headers: {
          Authorization: `Bearer ${process.env.OPENAI_API_KEY}`,
          'Content-Type': 'application/json',
        },
      }
    );

    // Send back the response from OpenAI
    context.res = {
      status: 200,
      body: response.data.choices[0].text,
    };
  } catch (error) {
    context.log.error('Error calling OpenAI API:', error);

    context.res = {
      status: 500,
      body: 'Error generating response from OpenAI API.',
    };
  }
};
