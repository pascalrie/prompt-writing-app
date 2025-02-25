import axios from "axios";

const getRandomPrompt = async () => {
    try {
        const response = await axios.get('http://localhost:8083/api/prompt/choose/random');
        return response.data;
    } catch (error) {
        console.error('Error fetching random prompt:', error);
        throw error;
    }
};

export default getRandomPrompt;