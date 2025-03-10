import { useEffect } from "react";
import axios from "axios";
import { useParams } from "react-router-dom";

const FetchCategoryDetails = ({ onFetch }) => {
    const { id } = useParams();

    useEffect(() => {
        const fetchCategoryDetails = async () => {
            try {
                const intId = parseInt(id, 10);
                const response = await axios.get(`http://localhost:8083/api/category/show/${intId}`);
                console.log(response.data);
                onFetch(response.data);
            } catch (error) {
                console.error("Error fetching category details:", error);
            }
        };

        fetchCategoryDetails();
    }, [id, onFetch]);

    return null;
};

export default FetchCategoryDetails;