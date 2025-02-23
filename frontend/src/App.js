import React, {useState, useEffect} from 'react';
import axios from 'axios';

const App = () => {
    const [prompt, setPrompt] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchPrompt = async () => {
            try {
                const response = await axios.get('http://localhost:8083/api/prompt/choose/random');
                setPrompt(response.data);
            } catch (e) {
                console.error(e);
                setError('Failed to fetch prompt.');
            }
        };

        fetchPrompt();
    }, []);

    return (
        <div>
            <h1>Prompt of the day</h1>
            {error ? (
                <p>{error}</p>
            ) : (
                <ul>
                    {prompt ? <li>{prompt.title}</li> : <p>Loading...</p>}
                </ul>
            )}
        </div>
    );
};
export default App;