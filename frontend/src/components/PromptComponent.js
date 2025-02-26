import React, { useEffect } from 'react';
import getRandomPrompt from '../fetchPrompt';

const PromptComponent = ({ prompt, setPrompt, setIsLoading }) => {
    useEffect(() => {
        const fetchPrompt = async () => {
            setIsLoading(true);
            try {
                const data = await getRandomPrompt();
                setPrompt(data);
            } catch (error) {
                console.error('Error fetching prompt:', error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchPrompt();
    }, [setPrompt, setIsLoading]);

    if (!prompt) {
        return null;
    }

    return (
        <div>
            <h3>Random Prompt: </h3>
            {prompt ? (
                <p>{prompt.title}</p>
            ) : (
                <p>Loading...</p>
            )}
        </div>
    );

};

export default PromptComponent;