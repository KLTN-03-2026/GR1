import { GoogleGenerativeAI } from "@google/generative-ai";

// 1. Khởi tạo với API Key của bạn
const genAI = new GoogleGenerativeAI("AIzaSyBrmHJ43k2kXENX5rEO2bAYqMmN7tsVphQ");

export const askGemini = async (prompt) => {
  try {
    // 2. Chọn mô hình (Gemini 1.5 Pro là bản mạnh nhất hiện nay)
    const model = genAI.getGenerativeModel({ model: "gemini-1.5-pro" });

    // 3. Gửi yêu cầu
    const result = await model.generateContent(prompt);
    const response = await result.response;
    return response.text();
  } catch (error) {
    console.error("Lỗi gọi Gemini:", error);
    return null;
  }
};