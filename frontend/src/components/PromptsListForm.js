import axios from "axios";
import { useEffect, useState } from "react";

const FetchPrompts = ({ onFetch }) => {
    useEffect(() => {
        const fetchPrompts = async () => {
            try {
                const response = await axios.get('http://localhost:8083/api/prompt/list');
                const promptsArray = Object.values(response.data).filter(item => item?.id && item?.title);
                onFetch(promptsArray);
            } catch (error) {
                console.error('Error fetching prompts:', error);
            }
        };
        fetchPrompts();
    }, [onFetch]);

    return null;
};

const PromptsListForm = () => {
    const [prompts, setPrompts] = useState([]);

    return (
        <div>
            <h1>Prompts</h1>
            <FetchPrompts onFetch={setPrompts} />
            <ul>
                {prompts.length > 0 ? (
                    prompts.map((prompts) => (
                        <li key={prompts.id}>{prompts.title}</li>
                    ))
                ) : (
                    <p>Loading prompts...</p>
                )}
            </ul>
        </div>
    );
};

export default PromptsListForm;