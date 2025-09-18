const express = require('express');
const axios = require('axios');
const app = express();
const port = process.env.PORT || 3002;

app.use(express.json());

app.get('/health', (req, res) => {
  res.json({ status: 'ok', service: 'shadow-comparator' });
});

app.get('/compare/:path*', async (req, res) => {
  const path = req.path.replace('/compare', '');
  const primaryUrl = process.env.PRIMARY_URL || 'http://app:80';
  const shadowUrl = process.env.SHADOW_URL || 'http://nginx-shadow:80';

  try {
    const [primaryResponse, shadowResponse] = await Promise.all([
      axios.get(`${primaryUrl}${path}`).catch(err => ({ error: err.message })),
      axios.get(`${shadowUrl}${path}`).catch(err => ({ error: err.message }))
    ]);

    res.json({
      path,
      primary: {
        status: primaryResponse.status || 'error',
        data: primaryResponse.data || primaryResponse.error
      },
      shadow: {
        status: shadowResponse.status || 'error',
        data: shadowResponse.data || shadowResponse.error
      },
      match: JSON.stringify(primaryResponse.data) === JSON.stringify(shadowResponse.data)
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.listen(port, () => {
  console.log(`Shadow comparator running on port ${port}`);
});